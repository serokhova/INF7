<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}', requirements: ['_locale' => 'fr|en|es|de|zh'], defaults: ['_locale' => 'fr'])]
class HomeController extends AbstractController
{
    #[Route('', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        $faqs = [
            ['q' => 'faq.q1', 'a' => 'faq.a1'],
            ['q' => 'faq.q2', 'a' => 'faq.a2']
        ];

        return $this->render('home/index.html.twig', [
            'faqs' => $faqs,
            'meta_title' => 'ColocChaleureuse - La gestion de colocation détendue et éco-responsable',
            'meta_desc' => 'Simplifiez la gestion de votre colocation : gestion des loyers, répartition équitable des tâches ménagères et calculs transparents.'
        ]);
    }
}