<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/chores', requirements: ['_locale' => 'fr|en|es|de|zh'])]
#[IsGranted('ROLE_USER')]
class ChoreController extends AbstractController
{
    #[Route('', name: 'app_chores', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('chore/index.html.twig', [
            'meta_title' => 'Semainier des tâches ménagères'
        ]);
    }
}
