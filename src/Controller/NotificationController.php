<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notification')]
class NotificationController extends AbstractController
{
    #[Route('/list', name: 'app_notification_list', methods: ['GET'])]
    public function list(NotificationRepository $notificationRepository, EntityManagerInterface $em)
    {

        foreach($this->getUser()->getNotifications() as $notification)
        // for each notification in notifications from the user in session
        // this function will help us select all the notifications from the user's list and set the isRead attribute to true
        {
            $notification->setIsRead(1);
            // setting isRead
            $em->persist($notification);
            // persisting in our DB
        }

        $em->flush();
        // flushing the data
        // it would also work if we did $notificationRepository->add($notification, true) to persist and flush

        return $this->render('notification/index.html.twig', [
            'notifications' => $notificationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_notification_new', methods: ['GET', 'POST'])]
    public function new(NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser();
        // setting the user in session in a variable

        foreach($user->getFollowers() as $follower)
        // for each follower from a user's list
        {
            $notification = new Notification();
            // creating a new object
            $notification->setDescription($user->getNickname()." a crée un nouveau post.");
            // setting it's description
            $notification->setUser($follower);
            // and the user that will get this notification

            // Sends notifications to users
            $follower->addNotification($notification);
        }
        
        $notificationRepository->add($notification, true);
        // persist and flush

        return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
        // returning the user to the list of subjects
        
    }

    #[Route('/{id}', name: 'app_notification_delete', methods: ['POST', 'GET'])]
    public function delete(Notification $notification ,NotificationRepository $notificationRepository): Response
    {
        
        $notificationRepository->remove($notification, true);
        // Delete a notification from DB
        // The function remove() helps us remove, persist and flush in one line

        return $this->redirectToRoute("app_notification_list", [], Response::HTTP_SEE_OTHER);
        // Redirection to our list of notifications
    }
}