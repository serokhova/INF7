<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\Household;
use App\Entity\User;
use App\Form\ExpenseType;
use App\Repository\ExpenseRepository;
use App\Repository\HouseholdRepository;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/owner', requirements: ['_locale' => 'fr|en'])]
#[IsGranted('ROLE_OWNER')]
class OwnerController extends AbstractController
{
    #[Route('/dashboard', name: 'app_owner_dashboard', methods: ['GET'])]
    public function dashboard(
        HouseholdRepository $households,
        ExpenseRepository $expenses,
        PaymentRepository $payments
    ): Response {
        /** @var User $owner */
        $owner = $this->getUser();
        $myHouseholds = $households->findBy(['owner' => $owner]);
        $period = date('Y-m');

        $byHousehold = [];
        foreach ($myHouseholds as $h) {
            $expByCat = $expenses->totalsByCategory($h, $period);
            $totalExpenses = array_sum($expByCat);
            $received = $payments->totalReceivedForHousehold($h, $period);
            $byHousehold[] = [
                'household' => $h,
                'expenses_by_category' => $expByCat,
                'total_expenses' => $totalExpenses,
                'total_received' => $received,
                'balance' => $received - $totalExpenses,
            ];
        }

        return $this->render('owner/dashboard.html.twig', [
            'period' => $period,
            'households' => $byHousehold,
            'meta_title' => 'Tableau de Bord Propriétaire - ColocChaleureuse',
            'meta_desc' => 'Suivi financier complet, gestion des flux de trésorerie et dépenses de vos colocations.',
        ]);
    }

    #[Route('/household/{id}/expenses', name: 'app_owner_expenses', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function manageExpenses(
        Household $household,
        Request $request,
        ExpenseRepository $expenses,
        EntityManagerInterface $em
    ): Response {
        $this->assertOwns($household);

        $expense = new Expense();
        $expense->setHousehold($household);
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($expense);
            $em->flush();
            $this->addFlash('success', 'flash.expense_added');
            return $this->redirectToRoute('app_owner_expenses', ['id' => $household->getId()]);
        }

        return $this->render('owner/expenses.html.twig', [
            'household' => $household,
            'form' => $form->createView(),
            'expenses' => $expenses->findByHousehold($household),
            'meta_title' => 'Dépenses ' . $household->getName(),
        ]);
    }

    #[Route('/expense/{id}/delete', name: 'app_owner_expense_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function deleteExpense(Expense $expense, Request $request, EntityManagerInterface $em): Response
    {
        $household = $expense->getHousehold();
        $this->assertOwns($household);

        if (!$this->isCsrfTokenValid('delete-expense-' . $expense->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($expense);
        $em->flush();
        $this->addFlash('success', 'flash.expense_deleted');
        return $this->redirectToRoute('app_owner_expenses', ['id' => $household->getId()]);
    }

    #[Route('/household/{id}/tenants', name: 'app_owner_tenants', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function manageTenants(
        Household $household,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->assertOwns($household);

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('manage-tenants-' . $household->getId(), (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException();
            }

            $charges = (string) $request->request->get('monthly_charges', '0');
            $household->setMonthlyCharges(number_format((float) $charges, 2, '.', ''));

            foreach ($household->getTenants() as $tenant) {
                $tantieme = (string) $request->request->get('tantieme_' . $tenant->getId(), '0');
                $rent = (string) $request->request->get('rent_' . $tenant->getId(), '0');
                $tenant->setTantieme(number_format((float) $tantieme, 2, '.', ''));
                $tenant->setMonthlyRent(number_format((float) $rent, 2, '.', ''));
            }

            $em->flush();
            $this->addFlash('success', 'flash.tenants_updated');
            return $this->redirectToRoute('app_owner_tenants', ['id' => $household->getId()]);
        }

        return $this->render('owner/tenants.html.twig', [
            'household' => $household,
            'meta_title' => 'Locataires ' . $household->getName(),
        ]);
    }

    private function assertOwns(Household $household): void
    {
        if ($household->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
    }
}
