<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/api', requirements: ['_locale' => 'fr|en|es|de|zh'])]
#[IsGranted('ROLE_USER')]
class DashboardApiController extends AbstractController
{
    #[Route('/chores/summary', name: 'api_chores_summary', methods: ['GET'])]
    public function getChoresSummary(): JsonResponse
    {
        $data = [
            'week' => date('W - Y'),
            'completion_rate' => 78,
            'tasks_distribution' => [
                ['task' => 'Vaisselle', 'assigned_to' => 'Julien', 'status' => 'Terminé'],
                ['task' => 'Ménage Salon', 'assigned_to' => 'Sophie', 'status' => 'En cours'],
                ['task' => 'Poubelles', 'assigned_to' => 'Lucas', 'status' => 'En attente'],
            ]
        ];

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }
}
