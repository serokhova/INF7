<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardApiControllerTest extends WebTestCase
{
    public function testApiRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/api/tasks');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testApiTasksReturnsJsonWithBasicAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/api/tasks', [], [], [
            'PHP_AUTH_USER' => 'owner@coloc.local',
            'PHP_AUTH_PW' => 'owner123',
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $payload = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($payload);
        $this->assertNotEmpty($payload);
        $this->assertArrayHasKey('title', $payload[0]);
    }

    public function testApiHouseholdInfoForTenant(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/api/household/info', [], [], [
            'PHP_AUTH_USER' => 'sophie@coloc.local',
            'PHP_AUTH_PW' => 'tenant123',
        ]);
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('La Joyeuse Coloc de Paris', $data['name']);
        $this->assertSame(2, $data['tenants_count']);
    }
}
