<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/message')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'app_message_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository): Response
    {
        return $this->render('message/app.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MessageRepository $messageRepository): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $message->setSender($this->getUser());


            $messageRepository->add($message, true);

            return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/new.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Message $message, MessageRepository $messageRepository): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $messageRepository->add($message, true);

            return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('message/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message, MessageRepository $messageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $messageRepository->remove($message, true);
        }

        return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
    }


    

    #[Route('/send', name: 'send', methods: ['GET', 'POST'])]
    public function send(Request $request): Response
    {
        $message = new Message;
        // Création d'un nouvel objet "Message"

        $form = $this->createForm(MessageType::class, $message);
        // Création d'un formulaire en passant par la table "MessageFormType"
        
        $form->handleRequest($request);
        // Pour procéder la data du form, on doit passer par la méthode de Symfony "handleRequest()"

        if($form->isSubmitted() && $form->isValid()){
        // Si le formulaire est rempli et qu'il est valide :

            $message->setSender($this->getUser());
            // L'envoyeur sera toujours l'utilisateur en cours d'utilisation (en session)

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $this->addFlash("message", "Message envoyé avec succès.");
            // Message de réussite
            return $this->redirectToRoute("messages");
            // On redirige vers la liste des messages
        }

        return $this->render("message/send.html.twig", [
            "form" => $form->createView()
        ]);
    }


    #[Route('/received', name: 'received', methods: ['GET'])]
    public function received(): Response
    {
        return $this->render('message/recieved.html.twig');
        // Voir les messages reçus
    }



    #[Route('/sent', name: 'sent', methods: ['GET'])]
    public function sent(): Response
    {
        return $this->render('message/sent.html.twig');
        // Voir les messages envoyés
    }

    
    // #[Route('/read/{id}', name: 'read', methods: ['GET'])]
    // public function read(Message $message): Response
    // {
        // $message->setIsRead(true);
        // Passe l'attribut à true
        // Le message est lu

        // $em = $this->getDoctrine()->getManager();
        // $em->persist($message);
        // $em->flush();
        // Entre la modification à la BDD

        // return $this->render('message/read.html.twig', compact("message"));
        // Rédirection
    // }

    #[Route('/deleted/{id}', name: 'deleted', methods: ['GET'])]
    public function deleted(Message $message): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($message);
        $em->flush();
        // Efface le message de la BDD
        // Automatiquement le message ne sera plus sur le naviguateur 

        return $this->redirectToRoute("received");
        // Rédirection
    }
}
