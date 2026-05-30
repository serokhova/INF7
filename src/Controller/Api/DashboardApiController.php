<?php

namespace App\Controller\Api;

use App\Entity\Household;
use App\Entity\User;
use App\Repository\ChoreAssignmentRepository;
use App\Repository\HouseholdRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/api', requirements: ['_locale' => 'fr|en'])]
#[IsGranted('ROLE_USER')]
class DashboardApiController extends AbstractController
{
    #[Route('/tasks', name: 'api_tasks', methods: ['GET'])]
    public function listTasks(TaskRepository $tasks): JsonResponse
    {
        $payload = array_map(fn ($t) => [
            'id' => $t->getId(),
            'title' => $t->getTitle(),
            'description' => $t->getDescription(),
            'points' => $t->getPointsValue(),
        ], $tasks->findAll());

        return new JsonResponse($payload);
    }

    #[Route('/chores/summary', name: 'api_chores_summary', methods: ['GET'])]
    public function choresSummary(
        ChoreAssignmentRepository $assignments,
        HouseholdRepository $households
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        $household = $this->resolveHousehold($user, $households);

        if ($household === null) {
            return new JsonResponse(['error' => 'no_household'], 404);
        }

        $year = (int) date('o');
        $week = (int) date('W');
        $list = $assignments->findForWeek($household, $year, $week);

        $done = 0;
        $tasks = [];
        foreach ($list as $a) {
            if ($a->getStatus() === 'done') {
                $done++;
            }
            $tasks[] = [
                'task' => $a->getTask()->getTitle(),
                'day' => $a->getDay(),
                'assigned_to' => $a->getAssignedTo()->getFirstName(),
                'status' => $a->getStatus(),
            ];
        }

        return new JsonResponse([
            'week' => sprintf('%02d - %d', $week, $year),
            'completion_rate' => count($list) > 0 ? (int) round($done * 100 / count($list)) : 0,
            'tasks_distribution' => $tasks,
        ]);
    }

    #[Route('/household/info', name: 'api_household_info', methods: ['GET'])]
    public function householdInfo(HouseholdRepository $households): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $household = $this->resolveHousehold($user, $households);

        if ($household === null) {
            return new JsonResponse(['error' => 'no_household'], 404);
        }

        return new JsonResponse([
            'id' => $household->getId(),
            'name' => $household->getName(),
            'address' => $household->getAddress(),
            'tenants_count' => $household->getTenants()->count(),
            'monthly_charges' => (float) $household->getMonthlyCharges(),
        ]);
    }

    private function resolveHousehold(User $user, HouseholdRepository $households): ?Household
    {
        if ($user->getHousehold() !== null) {
            return $user->getHousehold();
        }
        if (in_array('ROLE_OWNER', $user->getRoles(), true)) {
            return $households->findOneBy(['owner' => $user]);
        }
        return null;
    }
}
