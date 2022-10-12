<?php

namespace App\Controller;

use App\Repository\DirectMessageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/direct-message')]
class DirectMessageController extends AbstractController
{
    #[Route('/', name: 'app_direct_message_index', methods: ['GET'])]
    public function index(DirectMessageRepository $directMessageRepository): Response
    {
        return $this->render('direct_message/index.html.twig', [
            'messages' => $directMessageRepository->findAll(),
        ]);
    }
}
