<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\User;
use App\Form\PayRentType;
use App\Repository\ExpenseRepository;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/tenant', requirements: ['_locale' => 'fr|en'])]
#[IsGranted('ROLE_TENANT')]
class TenantController extends AbstractController
{
    #[Route('/dashboard', name: 'app_tenant_dashboard', methods: ['GET'])]
    public function dashboard(PaymentRepository $payments, ExpenseRepository $expenses): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $household = $user->getHousehold();
        $period = date('Y-m');

        $expensesByCat = $household ? $expenses->totalsByCategory($household, $period) : [];
        $totalCharges = array_sum($expensesByCat);
        $tantieme = (float) ($user->getTantieme() ?? 0);
        $userShare = round($totalCharges * $tantieme / 100, 2);

        return $this->render('tenant/dashboard.html.twig', [
            'user' => $user,
            'household' => $household,
            'payments' => $user ? $payments->findByTenant($user) : [],
            'tantieme' => $tantieme,
            'period' => $period,
            'expenses_by_category' => $expensesByCat,
            'total_charges' => $totalCharges,
            'user_share' => $userShare,
            'meta_title' => 'Mon Espace Locataire - ColocChaleureuse',
            'meta_desc' => 'Consultez vos quittances de loyer, suivez vos paiements mensuels et votre quote-part de charges.',
        ]);
    }

    #[Route('/pay', name: 'app_tenant_pay', methods: ['GET', 'POST'])]
    public function pay(Request $request, EntityManagerInterface $em, ExpenseRepository $expenses): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $household = $user->getHousehold();

        if ($household === null) {
            $this->addFlash('error', 'flash.no_household');
            return $this->redirectToRoute('app_tenant_dashboard');
        }

        $form = $this->createForm(PayRentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $period = $form->get('period')->getData();
            $tantieme = (float) ($user->getTantieme() ?? 0);
            $monthlyExpenses = array_sum($expenses->totalsByCategory($household, $period));
            $charges = round($monthlyExpenses * $tantieme / 100, 2);

            $payment = new Payment();
            $payment->setTenant($user)
                    ->setHousehold($household)
                    ->setPeriod($period)
                    ->setRentAmount($user->getMonthlyRent() ?? '0.00')
                    ->setChargesAmount(number_format($charges, 2, '.', ''))
                    ->setStatus(Payment::STATUS_PAID)
                    ->setPaidAt(new \DateTimeImmutable());

            $em->persist($payment);
            $em->flush();

            $this->addFlash('success', 'flash.payment_recorded');
            return $this->redirectToRoute('app_tenant_receipt', ['id' => $payment->getId()]);
        }

        return $this->render('tenant/pay.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'meta_title' => 'Payer mon loyer',
        ]);
    }

    #[Route('/receipts/{id}', name: 'app_tenant_receipt', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function receipt(Payment $payment): Response
    {
        if ($payment->getTenant() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('tenant/receipt.html.twig', [
            'payment' => $payment,
            'meta_title' => 'Quittance ' . $payment->getPeriod(),
        ]);
    }
}
