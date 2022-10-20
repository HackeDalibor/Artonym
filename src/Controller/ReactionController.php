<?php

namespace App\Controller;

use App\Entity\Reaction;
use App\Form\ReactionType;
use App\Repository\ReactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reaction')]
class ReactionController extends AbstractController
{
    #[Route('/', name: 'app_reaction_index', methods: ['GET'])]
    public function index(ReactionRepository $reactionRepository): Response
    {
        
        return $this->render('reaction/index.html.twig', [
            'reactions' => $reactionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reaction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReactionRepository $reactionRepository): Response
    {
        $reaction = new Reaction();
        $form = $this->createForm(ReactionType::class, $reaction);
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            // when using nested forms, two or more buttons can have the same name;
            // in those cases, compare the button objects instead of the button names
            if ($form->getClickedButton() === $form->get('likes'))
            {
                $reaction->setLikes(true);
                $reaction->setUser($this->getUser());
                $reactionRepository->add($reaction, true);
            }

            return $this->redirectToRoute('app_reaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reaction/new.html.twig', [
            'reaction' => $reaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reaction_show', methods: ['GET'])]
    public function show(Reaction $reaction): Response
    {
        return $this->render('reaction/show.html.twig', [
            'reaction' => $reaction,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reaction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reaction $reaction, ReactionRepository $reactionRepository): Response
    {
        $form = $this->createForm(ReactionType::class, $reaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reactionRepository->add($reaction, true);

            return $this->redirectToRoute('app_reaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reaction/edit.html.twig', [
            'reaction' => $reaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reaction_delete', methods: ['POST'])]
    public function delete(Request $request, Reaction $reaction, ReactionRepository $reactionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reaction->getId(), $request->request->get('_token'))) {
            $reactionRepository->remove($reaction, true);
        }

        return $this->redirectToRoute('app_reaction_index', [], Response::HTTP_SEE_OTHER);
    }
}
