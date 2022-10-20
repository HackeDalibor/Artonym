<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Comment;
use App\Entity\Subject;
use App\Entity\Reaction;
use App\Form\CommentType;
use App\Form\SubjectType;
use App\Form\ReactionType;
use App\Repository\ImageRepository;
use App\Repository\CommentRepository;
use App\Repository\SubjectRepository;
use App\Services\NotificationService;
use App\Repository\ReactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/subject')]
class SubjectController extends AbstractController
{
    #[Route('/', name: 'app_subject_index', methods: ['GET'])]
    public function index(SubjectRepository $subjectRepository): Response
    {
        return $this->render('subject/index.html.twig', [
            'subjects' => $subjectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_subject_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger, SubjectRepository $subjectRepository,
                        ImageRepository $imageRepository, NotificationService $notificationService)
    {
        $subject =  new Subject();
        $form = $this->createForm(SubjectType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subject->setTitle($form->get('title')->getViewData());
            // $subject->setCategory($form->get('category')->getViewData());

            $user = $this->getUser();
            $subject->setUser($user);
            $subjectRepository->add($subject);

            //* Images taken from our view Form
            $imgs = $form->get('images')->get('data')->getViewData();

            //* We need some images for new posts so I made an if method wich says :
            //* if our variable $imgs isn't empty, we proceed.
            if (empty($imgs) === false) {

                //* For each image we find in $imgs :
                foreach ($imgs as  $img) {

                    //* We're creating a new object "Image"
                    $image = new Image();
                    $originalFilename = pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $data = $safeFilename.'-'.uniqid(). '.' . $img->guessExtension();

                    try {
                        $img->move(
                            $this->getParameter('images_directory'),
                            $data
                        );
                    } catch (fileException $e) {}

                    $image->setData($data);
                    $image->setSubject($subject);
                    $imageRepository->add($image, true);
                }

                $notificationService->newNotification($subject, $user);
                
                //TODO : Ici envoi d'email à user

                return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
                    
                }
            }
            return $this->renderForm('subject/new.html.twig', [
                'subject' => $subject,
                'form' => $form,
            ]);           
    }

    #[Route('/{id}', name: 'app_subject_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Subject $subject, CommentRepository $commentRepository,
                        NotificationService $notificationService, ReactionRepository $reactionRepository): Response
    {
        $user = $this->getUser();

        $reaction = new Reaction();
        $formReaction = $this->createForm(ReactionType::class, $reaction);
        $formReaction->handleRequest($request);
        if ($formReaction->isSubmitted())
        {
            // when using nested forms, two or more buttons can have the same name;
            // in those cases, compare the button objects instead of the button names
                $reaction->setSubject($subject);
                $reaction->setUser($user);
                if($formReaction->getClickedButton() === $formReaction->get('likes')) {
                    $reaction->setLikes(true);
                } else if ($formReaction->getClickedButton() === $formReaction->get('loves')) {
                    if(!$reactionRepository->getLikeByUser($user)) {
                        $reaction->setLoves(true);
                    } elseif($reactionRepository->getLikeByUser($user) && $reactionRepository->findBy(['loves' => false])) {
                        $reaction->setLoves(true);
                    } else {
                        $reaction->setLoves(false);
                    }
                } else if ($formReaction->getClickedButton() === $formReaction->get('dontLike')) {
                    if(count($reactionRepository->findBy(['user' => $user])) === 0) {
                        $reaction->setDontLike(true);
                    }
                } else if ($formReaction->getClickedButton() === $formReaction->get('wow')) {
                    if(count($reactionRepository->findBy(['user' => $user])) === 0) {
                        $reaction->setWow(true);
                    }
                } else if ($formReaction->getClickedButton() === $formReaction->get('funny')) {
                    if(count($reactionRepository->findBy(['user' => $user])) === 0) {
                        $reaction->setFunny(true);
                    }
                } else if ($formReaction->getClickedButton() === $formReaction->get('sad')) {
                    if(count($reactionRepository->findBy(['user' => $user])) === 0) {
                        $reaction->setSad(true);
                    }
                }
                $reactionRepository->add($reaction, true);
            
            
            
            } 

        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class);
        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid())
        {
            $comment->setText($formComment->get('text')->getViewData());
            $comment->setUser($user);
            $comment->setSubject($subject);
            $commentRepository->add($comment, true);
            $notificationService->newNotification($comment, $subject->getUser());
        }

        return $this->render('subject/show.html.twig', [
            'subject' => $subject,
            'formComment' => $formComment->createView(),
            'formReaction' => $formReaction->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_subject_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Subject $subject, SubjectRepository $subjectRepository): Response
    {
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subjectRepository->add($subject, true);

            return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('subject/edit.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subject_delete', methods: ['POST'])]
    public function delete(Request $request, Subject $subject, SubjectRepository $subjectRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subject->getId(), $request->request->get('_token'))) {
            $subjectRepository->remove($subject, true);
        }

        return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/liked/{id}', name: 'app_subject_liked', methods: ['GET', 'POST'])]
    public function likesSubject(SubjectRepository $subjectRepository, Subject $subject, CommentRepository $commentRepository,
                                Request $request, NotificationService $notificationService): Response
    {        
        $user = $this->getUser();

        $subject->addLikedBy($user);

        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class);
        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid())
        {
            $comment->setText($formComment->get('text')->getViewData());
            $comment->setUser($user);
            $comment->setSubject($subject);
            $commentRepository->add($comment, true);
            $notificationService->newNotification($comment, $subject->getUser());
        }

        $subjectRepository->add($subject, true);
        
        return $this->render('subject/show.html.twig', [
            'subject' => $subject,
            'formComment' => $formComment->createView(),
        ]);
    }

    #[Route('/unliked/{id}', name: 'app_subject_unliked', methods: ['GET', 'POST'])]
    public function unlikeSubject(SubjectRepository $subjectRepository, Subject $subject, CommentRepository $commentRepository,
                                  Request $request, NotificationService $notificationService): Response
    {        
        $user = $this->getUser();

        $subject->removeLikedBy($user);

        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class);
        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid())
        {
            $comment->setText($formComment->get('text')->getViewData());
            $comment->setUser($user);
            $comment->setSubject($subject);
            $commentRepository->add($comment, true);
            $notificationService->newNotification($comment, $subject->getUser());
        }

        $subjectRepository->add($subject, true);
        
        return $this->render('subject/show.html.twig', [
            'subject' => $subject,
            'formComment' => $formComment->createView(),
        ]);

    }
}
