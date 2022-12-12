<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('target_path');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route('/profile', name: 'app_security_profile', methods: ['GET'])]
    public function showSelf(): Response
    {
        return $this->render('security/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/profile/followers', name: 'app_security_followers', methods: ['GET'])]
    public function showFollowers(): Response
    {
        return $this->render('security/followers.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/profile/followings', name: 'app_security_followings', methods: ['GET'])]
    public function showFollowings(): Response
    {
        return $this->render('security/followings.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
