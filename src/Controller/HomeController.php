<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_root', methods: ['GET'])]
    public function root(): RedirectResponse
    {
        return $this->redirectToRoute('app_home', ['_locale' => 'fr']);
    }

    #[Route('/{_locale}', name: 'app_home', requirements: ['_locale' => 'fr|en'], methods: ['GET'])]
    public function index(): Response
    {
        $faqs = [
            ['q' => 'faq.q1', 'a' => 'faq.a1'],
            ['q' => 'faq.q2', 'a' => 'faq.a2'],
            ['q' => 'faq.q3', 'a' => 'faq.a3'],
        ];

        return $this->render('home/index.html.twig', [
            'faqs' => $faqs,
            'meta_title' => 'ColocChaleureuse — Gestion de colocation responsable',
            'meta_desc' => 'Simplifiez la gestion de votre colocation : loyers, charges, tâches ménagères et messagerie. Éco-conçu et sécurisé.',
        ]);
    }
}
