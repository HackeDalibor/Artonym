<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Subject;
use App\Form\ImageType;
use App\Form\SubjectType;
use App\Services\FileUploader;
use App\Repository\ImageRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/multipleImageInsert/', name: 'app_subject_multiple_instert')]
    public function multipleImageInsert(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $subject =  new Subject();
        $form = $this->createForm(SubjectType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subject->setTitle($form->get('title')->getViewData());

            $em->persist($subject);

            $imgs = $form->get('images')->get('data')->getViewData();

            if (empty($imgs) === false) {
                foreach ($imgs as  $img) {

                    $image = new Image();
                    $originalFilename =  pathinfo($img->getClientOriginalName(), PATHINFO_FILENAME);
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
                    $em->persist($image);
                }

                    $subject->setUser($this->getUser());

                    //TODO : Ici envoi d'email à user et création de notif

                    $em->flush();

                    return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
                    
                }
            }
            return $this->renderForm('subject/newMultiple.html.twig', [
                'subject' => $subject,
                'form' => $form,
            ]);           
    }

    #[Route('/new', name: 'app_subject_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SubjectRepository $subjectRepository, EntityManagerInterface $objectManager, FileUploader $fileUploader): Response
    {
        $subject = new Subject();
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);
        
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $subject->setUser($this->getUser());
            $images = $subject->getImages();
            // $subjectRepository->add($subject, true);
            
            
            $imageFiles = $form->get('images')->getData();

            
            foreach($images as $image)
            {
                
                // $image->setData($imageFile);

                // $image->setSubject($subject);
                // $objectManager->persist($image);
                
                foreach($imageFiles as $imageFile){
                    // dd($imageFile);
                    if($imageFile){

                       
                        if ($imageFile) {
                            $data = $fileUploader->upload($imageFile);
                            $image->setData($data);
                        }

                    }
                }

            }

            $objectManager->flush();

            return $this->redirectToRoute('app_subject_index', [], Response::HTTP_SEE_OTHER);
        

        }

        return $this->renderForm('subject/new.html.twig', [
            'subject' => $subject,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subject_show', methods: ['GET'])]
    public function show(Subject $subject): Response
    {
        return $this->render('subject/show.html.twig', [
            'subject' => $subject,
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
}
