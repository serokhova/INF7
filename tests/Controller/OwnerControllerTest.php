<?php

namespace App\Tests\Controller;

use App\Entity\Expense;
use App\Entity\Household;
use App\Entity\User;
use App\Repository\ExpenseRepository;
use App\Repository\HouseholdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OwnerControllerTest extends WebTestCase
{
    public function testOwnerDashboardShowsHouseholdSummary(): void
    {
        $client = static::createClient();
        /** @var UserRepository $repo */
        $repo = static::getContainer()->get(UserRepository::class);
        $owner = $repo->findOneByEmail('owner@coloc.local');
        $client->loginUser($owner);

        $client->request('GET', '/fr/owner/dashboard');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Espace Gestion Propriétaire');
        $this->assertSelectorTextContains('body', 'La Joyeuse Coloc');
    }

    public function testOwnerCanAddAndDeleteExpense(): void
    {
        $client = static::createClient();
        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);
        /** @var HouseholdRepository $houseRepo */
        $houseRepo = static::getContainer()->get(HouseholdRepository::class);
        /** @var ExpenseRepository $expenseRepo */
        $expenseRepo = static::getContainer()->get(ExpenseRepository::class);

        $owner = $userRepo->findOneByEmail('owner@coloc.local');
        $client->loginUser($owner);
        $household = $houseRepo->findOneBy(['owner' => $owner]);
        $this->assertInstanceOf(Household::class, $household);

        $client->request('GET', '/fr/owner/household/' . $household->getId() . '/expenses');
        $this->assertResponseIsSuccessful();

        $period = date('Y-m');
        $client->submitForm('Ajouter', [
            'expense[category]' => 'water',
            'expense[label]' => 'Test eau',
            'expense[amount]' => '42.50',
            'expense[period]' => $period,
        ]);
        $this->assertResponseRedirects();

        $found = $expenseRepo->findOneBy(['label' => 'Test eau']);
        $this->assertInstanceOf(Expense::class, $found);
    }
}
