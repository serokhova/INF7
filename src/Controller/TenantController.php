<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/tenant', requirements: ['_locale' => 'fr|en|es|de|zh'])]
#[IsGranted('ROLE_TENANT')]
class TenantController extends AbstractController
{
    #[Route('/dashboard', name: 'app_tenant_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        $user = $this->getUser();
        
        $paymentHistory = [
            ['date' => '2026-04-05', 'amount' => 450.00, 'status' => 'Payé', 'type' => 'Loyer + Charges'],
            ['date' => '2026-03-05', 'amount' => 450.00, 'status' => 'Payé', 'type' => 'Loyer + Charges'],
        ];

        return $this->render('tenant/dashboard.html.twig', [
            'user' => $user,
            'household' => $user->getHousehold(),
            'payments' => $paymentHistory,
            'tantieme' => 25,
            'meta_title' => 'Mon Espace Locataire - ColocChaleureuse',
            'meta_desc' => 'Consultez vos quittances de loyer et suivez vos paiements mensuels.'
        ]);
    }
}
