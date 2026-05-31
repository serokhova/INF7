<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChoreControllerTest extends WebTestCase
{
    public function testChoresWeekRendersForTenant(): void
    {
        $client = static::createClient();
        /** @var UserRepository $repo */
        $repo = static::getContainer()->get(UserRepository::class);
        $tenant = $repo->findOneByEmail('sophie@coloc.local');
        $client->loginUser($tenant);

        $client->request('GET', '/fr/chores');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Semainier');
        foreach (['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'] as $day) {
            $this->assertSelectorTextContains('body', $day);
        }
    }

    public function testAnonymousCannotSeeChores(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/chores');
        $this->assertResponseRedirects();
    }
}
