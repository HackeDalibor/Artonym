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
        {
            $notification->setIsRead(1);
            $em->persist($notification);
        }

        $em->flush();

        return $this->render('notification/index.html.twig', [
            'notifications' => $notificationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_notification_new', methods: ['GET', 'POST'])]
    public function new(NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser();

        foreach($user->getFollowers() as $follower)
        {
            $notification = new Notification();
            $notification->setDescription($user->getNickname()." a crée un nouveau post.");
            $notification->setUser($follower);

            //* Sends notifications to users
            $follower->addNotification($notification);
        }
        
        $notificationRepository->add($notification, true);

        return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
        
    }

    #[Route('/{id}', name: 'app_notification_delete', methods: ['POST', 'GET'])]
    public function delete(Notification $notification ,NotificationRepository $notificationRepository): Response
    {
        
        $notificationRepository->remove($notification, true);
        // Delete a notification from DB
        // The notification will automatically dissapear online 

        return $this->redirectToRoute("app_notification_list", [], Response::HTTP_SEE_OTHER);
        // Redirection
    }
}