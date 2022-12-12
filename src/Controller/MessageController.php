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
        return $this->render('message/index.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }

    #[Route('/send', name: 'app_message_send', methods: ['GET', 'POST'])]
    public function send(Request $request, EntityManagerInterface $em): Response
    {

        $user = $this->getUser();

        $message = new Message;
        // Création d'un nouvel objet "Message"

        $form = $this->createForm(MessageType::class, $message, ['user' => $user, 'id' => $user->getId()]);
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


    #[Route('/received', name: 'app_message_received', methods: ['GET'])]
    public function received(): Response
    {
        return $this->render('message/recieved.html.twig');
        // See every recieved message
    }



    #[Route('/sent', name: 'app_message_sent', methods: ['GET'])]
    public function sent(): Response
    {
        return $this->render('message/sent.html.twig');
        // See every sent message
    }

    
    #[Route('/read/{id}', name: 'app_message_read', methods: ['GET'])]
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

    #[Route('/delete/{id}', name: 'app_message_delete', methods: ['GET'])]
    public function deleted(Message $message, EntityManagerInterface $em): Response
    {
 
        $em->remove($message);
        $em->flush();
        // Delete a message from DB
        // The message will automatically dissapear online 

        return $this->redirectToRoute("app_message_received");
        // Redirection
    }
}
