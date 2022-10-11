<?php

namespace App\Services;

use App\Entity\User;
use App\Entity\Notification;
use App\Repository\NotificationRepository;

class NotificationService
{
    private NotificationRepository $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function newNotification($object, User $user)
    {
        if (get_class($object) === 'App\Entity\Subject') {
            foreach($user->getFollowers() as $follower)
            {
                $notification = new Notification();
                $notification->setDescription($user->getNickname()." created a new post.");
                $notification->setUser($follower);
                $follower->addNotification($notification);
                $this->notificationRepository->add($notification, true);
            }
        } elseif (get_class($object) === 'App\Entity\Comment') {

            $notification = new Notification();
            $notification->setDescription("You have a new comment on your post.");
            $notification->setUser($user);
            $user->addNotification($notification);
            $this->notificationRepository->add($notification, true);

        }
        // TODO : When Reaction entity is done this will create the new notification 
        // elseif (get_class($object) === 'App\Entity\Su') {
        //     foreach($user->getLikedSubjects() as $likedSubject)
        //     {
        //         $notification = new Notification();
        //         $notification->setDescription($user->getNickname()." liked your post.");
        //         $notification->setUser($likedSubject->getUser());
        //         $likedSubject->getUser()->addNotification($notification);
        //         $this->notificationRepository->add($notification, true);
        //     }
        // }
    }

}
