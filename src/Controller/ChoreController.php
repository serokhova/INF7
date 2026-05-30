<?php

namespace App\Controller;

use App\Entity\ChoreAssignment;
use App\Entity\Household;
use App\Entity\User;
use App\Form\ChoreAssignmentType;
use App\Repository\ChoreAssignmentRepository;
use App\Repository\HouseholdRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/chores', requirements: ['_locale' => 'fr|en'])]
#[IsGranted('ROLE_USER')]
class ChoreController extends AbstractController
{
    #[Route('', name: 'app_chores', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        ChoreAssignmentRepository $assignments,
        TaskRepository $tasks,
        HouseholdRepository $households,
        EntityManagerInterface $em
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $household = $this->resolveHousehold($user, $households);

        if ($household === null) {
            return $this->render('chore/index.html.twig', [
                'household' => null,
                'meta_title' => 'Semainier des tâches ménagères',
            ]);
        }

        $year = (int) date('o');
        $week = (int) date('W');

        $newAssignment = new ChoreAssignment();
        $newAssignment->setHousehold($household);
        $newAssignment->setYear($year);
        $newAssignment->setWeekNumber($week);

        $form = $this->createForm(ChoreAssignmentType::class, $newAssignment, [
            'household' => $household,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->isMember($user, $household)) {
            $em->persist($newAssignment);
            $em->flush();
            $this->addFlash('success', 'flash.assignment_added');
            return $this->redirectToRoute('app_chores');
        }

        $weekAssignments = $assignments->findForWeek($household, $year, $week);
        $byDay = array_fill_keys(ChoreAssignment::DAYS, []);
        foreach ($weekAssignments as $a) {
            $byDay[$a->getDay()][] = $a;
        }

        return $this->render('chore/index.html.twig', [
            'household' => $household,
            'year' => $year,
            'week' => $week,
            'by_day' => $byDay,
            'form' => $form->createView(),
            'all_tasks' => $tasks->findAll(),
            'can_assign' => $this->isMember($user, $household),
            'meta_title' => 'Semainier des tâches ménagères',
        ]);
    }

    #[Route('/{id}/done', name: 'app_chores_done', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function markDone(
        ChoreAssignment $assignment,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        if (!$this->isCsrfTokenValid('chore-done-' . $assignment->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }
        if ($assignment->getAssignedTo() !== $user && $assignment->getHousehold()->getOwner() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $assignment->setStatus(ChoreAssignment::STATUS_DONE);
        $em->flush();
        $this->addFlash('success', 'flash.chore_done');
        return $this->redirectToRoute('app_chores');
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

    private function isMember(User $user, Household $household): bool
    {
        return $household->getOwner() === $user || $user->getHousehold() === $household;
    }
}
