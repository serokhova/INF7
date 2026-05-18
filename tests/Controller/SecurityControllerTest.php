<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageIsUp(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testProtectedOwnerDashboardForbidsTenant(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get('doctrine')->getRepository(\App\Entity\User::class);
        
        $testTenant = $userRepository->findOneByEmail('sophie@coloc.local');
        if ($testTenant) {
            $client->loginUser($testTenant);
            $client->request('GET', '/fr/owner/dashboard');
            $this->assertResponseStatusCodeSame(403);
        }
    }
}
