<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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

    public function testRegisterPageIsUp(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Créer mon compte');
    }

    public function testForgotPasswordGeneratesLinkInDemo(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/forgot-password');
        $this->assertResponseIsSuccessful();

        $client->submitForm('Envoyer le lien', [
            'forgot_password[email]' => 'sophie@coloc.local',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.flash-info', 'lien de réinitialisation');
    }

    public function testOwnerCannotAccessTenantArea(): void
    {
        $client = static::createClient();
        /** @var UserRepository $repo */
        $repo = static::getContainer()->get(UserRepository::class);
        $owner = $repo->findOneByEmail('owner@coloc.local');
        $this->assertInstanceOf(User::class, $owner);

        $client->loginUser($owner);
        $client->request('GET', '/fr/tenant/dashboard');
        $this->assertResponseStatusCodeSame(403);
    }

    public function testTenantCannotAccessOwnerArea(): void
    {
        $client = static::createClient();
        /** @var UserRepository $repo */
        $repo = static::getContainer()->get(UserRepository::class);
        $tenant = $repo->findOneByEmail('sophie@coloc.local');
        $this->assertInstanceOf(User::class, $tenant);

        $client->loginUser($tenant);
        $client->request('GET', '/fr/owner/dashboard');
        $this->assertResponseStatusCodeSame(403);
    }

    public function testAnonymousIsRedirectedFromProtectedArea(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/owner/dashboard');
        $this->assertResponseRedirects();
    }
}
