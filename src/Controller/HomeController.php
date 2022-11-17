<?php

namespace App\Controller;

use App\Form\SearchResultsType;
use App\Repository\UserRepository;
use App\Repository\SubjectRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SubjectRepository $subjectRepository, CategoryRepository $categoryRepository): Response
    {
        if($this->getUser() === null) {

            return $this->redirectToRoute('app_login');

        } else {

            foreach($this->getUser()->getFollowing() as $following) {
                return $this->render('home/index.html.twig', [
                    'subjects' => $subjectRepository->findBy(['user' => $following, 'user' => $this->getUser()], ['creationDate' => 'DESC'], 15),
                    'categories' => $categoryRepository->findAll(),
                ]);
            }
            return $this->render('home/nofollowing.html.twig', [
                'categories' => $categoryRepository->findAll(),
            ]);
        }
    }

    #[Route('/search', name: 'app_home_search')]
    public function search(SubjectRepository $subjectRepository, Request $request, UserRepository $userRepository): Response
    {
        
        $form = $this->createForm(SearchResultsType::class);

        $search = $form->handleRequest($request);
            
        $users = $userRepository->findAll();
        $subjects = $subjectRepository->findAll();

        if($form->isSubmitted() && $form->isValid()) {

            if(count($userRepository->searchUser($search->get('keywords')->getData())) < 1) {

                if(count($subjectRepository->searchSubject($search->get('keywords')->getData())) < 1) {
                    return $this->redirectToRoute('app_home_search', [], Response::HTTP_SEE_OTHER);
                } else {
                    $subjects = $subjectRepository->searchSubject($search->get('keywords')->getData());
                }

            } else {
                $users = $userRepository->searchUser($search->get('keywords')->getData());
            }
        }

        return $this->render('home/search.html.twig', [
            'subjects' => $subjects,
            'users' => $users,
            'form' => $form->createView(),
        ]);

    }
}