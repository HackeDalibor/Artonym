<?php

namespace App\Controller;

use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SubjectRepository $subjectRepository): Response
    {
        if($this->getUser() === null) {

            return $this->redirectToRoute('app_login');

        } else {

            foreach($this->getUser()->getFollowing() as $following) {
                return $this->render('home/index.html.twig', [
                    'subjects' => $subjectRepository->findBy(['user' => $following], ['creationDate' => 'DESC'], 15),
                ]);
            }
            return $this->render('home/nofollowing.html.twig');
        }
    }

}