<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testRootRedirectsToFrenchHome(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseRedirects('/fr');
    }

    public function testFrenchHomeIsUp(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
        $this->assertSelectorExists('section#faq');
    }

    public function testEnglishHomeRenders(): void
    {
        $client = static::createClient();
        $client->request('GET', '/en');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome');
    }

    public function testSeoTagsArePresent(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr');

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $crawler->filter('meta[name="description"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('link[rel="canonical"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('link[rel="alternate"][hreflang]')->count());
        $this->assertGreaterThan(0, $crawler->filter('script[type="application/ld+json"]')->count());
    }

    public function testRobotsTxtExistsInPublicFolder(): void
    {
        $path = dirname(__DIR__, 2) . '/public/robots.txt';
        $this->assertFileExists($path);
        $this->assertStringContainsString('Disallow:', file_get_contents($path));
        $this->assertStringContainsString('Sitemap:', file_get_contents($path));
    }

    public function testSitemapXmlIsPublic(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('<urlset', $client->getResponse()->getContent());
        $this->assertStringContainsString('hreflang', $client->getResponse()->getContent());
    }
}
