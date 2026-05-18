<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/owner', requirements: ['_locale' => 'fr|en|es|de|zh'])]
#[IsGranted('ROLE_OWNER')]
class OwnerController extends AbstractController
{
    #[Route('/dashboard', name: 'app_owner_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        $analytics = [
            'total_revenue' => 1800.00,
            'expenses' => [
                'water' => 120.00,
                'electricity' => 240.00,
                'internet' => 39.99,
                'taxes' => 150.00
            ]
        ];

        return $this->render('owner/dashboard.html.twig', [
            'analytics' => $analytics,
            'meta_title' => 'Tableau de Bord Propriétaire - ColocChaleureuse',
            'meta_desc' => 'Suivi financier complet, gestion des flux de trésorerie et dépenses de vos colocations.'
        ]);
    }
}
