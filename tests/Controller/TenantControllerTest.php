<?php

namespace App\Tests\Controller;

use App\Entity\Payment;
use App\Entity\User;
use App\Repository\PaymentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TenantControllerTest extends WebTestCase
{
    private function loginAs(string $email): array
    {
        $client = static::createClient();
        /** @var UserRepository $repo */
        $repo = static::getContainer()->get(UserRepository::class);
        $user = $repo->findOneByEmail($email);
        $this->assertInstanceOf(User::class, $user);
        $client->loginUser($user);
        return [$client, $user];
    }

    public function testTenantDashboardShowsKeyFigures(): void
    {
        [$client, ] = $this->loginAs('sophie@coloc.local');
        $client->request('GET', '/fr/tenant/dashboard');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Tableau de Bord Locataire');
        $this->assertSelectorTextContains('body', 'La Joyeuse Coloc');
        $this->assertSelectorTextContains('body', 'Votre quote-part');
    }

    public function testTenantCanPayRent(): void
    {
        [$client, $tenant] = $this->loginAs('sophie@coloc.local');
        $period = date('Y-m');

        $client->request('GET', '/fr/tenant/pay');
        $this->assertResponseIsSuccessful();

        $client->submitForm('Confirmer le paiement', [
            'pay_rent[period]' => $period,
        ]);

        $this->assertResponseRedirects();

        /** @var PaymentRepository $payments */
        $payments = static::getContainer()->get(PaymentRepository::class);
        $found = $payments->findOneBy(['tenant' => $tenant, 'period' => $period]);
        $this->assertInstanceOf(Payment::class, $found);
        $this->assertSame('paid', $found->getStatus());
    }
}
