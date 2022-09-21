<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    // #[Route('/new', name: 'app_message_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, MessageRepository $messageRepository): Response
    // {
    //     $message = new Message();
    //     $form = $this->createForm(MessageType::class, $message);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {

    //         $message->setSender($this->getUser());


    //         $messageRepository->add($message, true);

    //         return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('message/new.html.twig', [
    //         'message' => $message,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_message_show', methods: ['GET'])]
    // public function show(Message $message): Response
    // {
    //     return $this->render('message/show.html.twig', [
    //         'message' => $message,
    //     ]);
    // }

    // #[Route('/{id}/edit', name: 'app_message_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Message $message, MessageRepository $messageRepository): Response
    // {
    //     $form = $this->createForm(MessageType::class, $message);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $messageRepository->add($message, true);

    //         return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('message/edit.html.twig', [
    //         'message' => $message,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_message_delete', methods: ['POST'])]
    // public function delete(Request $request, Message $message, MessageRepository $messageRepository): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
    //         $messageRepository->remove($message, true);
    //     }

    //     return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
    // }


    

    #[Route('/send', name: 'send', methods: ['GET', 'POST'])]
    public function send(Request $request, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();

        // dd($this->getUser());
        $message = new Message;
        // Création d'un nouvel objet "Message"


        $form = $this->createForm(MessageType::class, $message, ['user' => $user]);
        // Création d'un formulaire en passant par la table "MessageFormType"
        
        $form->handleRequest($request);
        // Pour procéder la data du form, on doit passer par la méthode de Symfony "handleRequest()"
        
        
        if($form->isSubmitted() && $form->isValid()){
            // Si le formulaire est rempli et qu'il est valide : 

            $message->setSender($user);
            // L'envoyeur sera toujours l'utilisateur en cours d'utilisation (en session)
            
            $em->persist($message);
            $em->flush();

            $this->addFlash("message", "Message sent with success.");
            // Message de réussite
            return $this->redirectToRoute("app_message_index");
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
        // See every recieved message
    }



    #[Route('/sent', name: 'sent', methods: ['GET'])]
    public function sent(): Response
    {
        return $this->render('message/sent.html.twig');
        // See every sent message
    }

    
    #[Route('/read/{id}', name: 'read', methods: ['GET'])]
    public function read(Message $message, EntityManagerInterface $em): Response
    {
        $message->setIsRead(true);
        // Passe l'attribut à true
        // Le message est lu

        $em->persist($message);
        $em->flush();
        // Entre la modification à la BDD

        return $this->render('message/read.html.twig', compact("message"));
    }

    #[Route('/deleted/{id}', name: 'deleted', methods: ['GET'])]
    public function deleted(Message $message, EntityManagerInterface $em): Response
    {
 
        $em->remove($message);
        $em->flush();
        // Delete a message from DB
        // Automatically the message will dissapear online 

        return $this->redirectToRoute("received");
        // Redirection
    }
}
