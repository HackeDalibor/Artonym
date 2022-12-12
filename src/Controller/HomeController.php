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
        // the method getUser() will help us get the connected user
        // if the user is not connected / if there is no users

            return $this->redirectToRoute('app_login');
            // we will return to the login page
            // the idea is to not let any user to navigate if he's not connected

        } else {

            foreach($this->getUser()->getFollowing() as $following) {
            // if the user is connected he'll land on a homepage with all the subjects of the people he follows
            // but first I need to find each person he follows

                return $this->render('home/index.html.twig', [
                //  sending it to the page with the infos he'll see0

                    'subjects' => $subjectRepository->findBy(['user' => [$following, $this->getUser()]], ['creationDate' => 'DESC'], 5),
                    // findBy() is a function made by Symfony that'll help us get all of the data by options inside of that function
                    // in this case we're getting every subject that has either the person we're following or our own subject
                    // it's ordered by the last subject created and we're taking only 5 subjects at a time

                    'categories' => $categoryRepository->findAll(),
                    // findAll() is a different function, this one will help us get every data with no parameters
                    // we're taking every category that exists
                ]);
            }
            return $this->render('home/nofollowing.html.twig', [
            // if ever the user just created his account, he won't have any users followed not subjects made
            // we'll need a new page for him so we won't have any errors and to greet him at the first place
            // in the other place I could make some suggestions in here

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
                $subjects = $subjectRepository->findBy(['user' => $users], ['creationDate' => 'DESC'], 30);
            }
        }

        return $this->render('home/search.html.twig', [
            'subjects' => $subjects,
            'users' => $users,
            'form' => $form->createView(),
        ]);

    }
}