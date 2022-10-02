<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notification')]
class NotificationController extends AbstractController
{
    #[Route('/list', name: 'app_notification_list', methods: ['GET'])]
    public function list(NotificationRepository $notificationRepository)
    {
        return $this->render('notification/index.html.twig', [
            'notifications' => $notificationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_notification_new', methods: ['GET', 'POST'])]
    public function new(NotificationRepository $notificationRepository, string $module = 'toto'): Response
    {
        $user = $this->getUser();

        foreach($user->getFollowers() as $follower)
        {
            $notification = new Notification();
            $notification->setDescription($user->getNickname()." a crée un nouveau post.");
            $notification->setModule($module);
            $notification->setUser($follower);
        }
        
        //? Envoyer les notifs aux users

        $notificationRepository->add($notification, true);

        return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
        
    }

    #[Route('/{id}', name: 'app_notification_delete', methods: ['POST'])]
    public function delete(Request $request, Notification $notification, NotificationRepository $notificationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$notification->getId(), $request->request->get('_token'))) {
            $notificationRepository->remove($notification, true);
        }

        return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
    }
}