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
    // the object will be the one we create at the moment and we also need to identify our user
    {
        if (get_class($object) === 'App\Entity\Subject') {
        // get_class() is a function wich will return the name of the class of an object
        // if the get_class of our object is 100% equal to 'App\Entity\Subject', then :

            foreach($user->getFollowers() as $follower)
            // for each follower from the users list we'll send a notification when he creates a post, so :
            {
                // firstly we'll create our new notification
                $notification = new Notification();

                // then we're setting the attributes, such as the description and the user that will get this 
                $notification->setDescription($user->getNickname()." created a new post.");
                $notification->setUser($follower);
                $follower->addNotification($notification);

                // persist and flush via repository
                $this->notificationRepository->add($notification, true);
            }
        } elseif (get_class($object) === 'App\Entity\Comment') {
        // ... but if our object is from the class "Comment", then :

            $notification = new Notification();

            $notification->setDescription($object->getUser()->getNickname().' commented "'.$object.'" on your post called '.$object->getSubject()->getTitle().'.');
            // since the object will be a comment that's creating, we can extract the user that created it and the subject that has the comment

            $notification->setUser($user);
            // in this case the user will be the one that created the subject in wich the comment stays
            $user->addNotification($notification);
            $this->notificationRepository->add($notification, true);

        }
        // TODO : When Reaction entity is done this will create the new notification 
        // elseif (get_class($object) === 'App\Entity\Reacton') {
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
