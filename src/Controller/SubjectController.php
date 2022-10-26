<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Comment;
use App\Entity\Subject;
use App\Entity\Reaction;
use App\Form\CommentType;
use App\Form\SubjectType;
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

    #[Route('/reacted/{id}', name: 'app_subject_reacted', methods: ['GET', 'POST'])]
    public function reacts(Subject $subject, SubjectRepository $subjectRepository, ReactionRepository $reactionRepository, NotificationService $notificationService): Response
    {        
        $user = $this->getUser();

        if(!$user) return $this->json(['code' => 403, 'message' => "Unauthorised"], 403);

        if($subject->isReactedByUser($user))
        {
            $like = $reactionRepository->findOneBy([
                'subject' => $subject,
                'user' => $user
            ]);

            $reactionRepository->remove($like, true);
            return $this->json([
                'code' => 200,
                'message' => "You have unliked this subject",
                'reactions' => $reactionRepository->count(['subject' => $subject])
            ], 200);
        }

        $reaction = new Reaction();
        $reaction->setSubject($subject)
                ->setUser($user)
                ->setLikes(true);

        $reactionRepository->add($reaction, true);

        return $this->json([
            'code' => 200,
            'message' => "You liked this subject",
            'reactions' => $reactionRepository->count(['subject' => $subject])
        ], 200);
    }
}
