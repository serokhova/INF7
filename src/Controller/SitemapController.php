<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap', methods: ['GET'])]
    public function sitemap(): Response
    {
        $publicRoutes = ['app_home', 'app_login', 'app_register', 'app_forgot_password'];
        $locales = ['fr', 'en'];
        $today = date('Y-m-d');

        $urls = [];
        foreach ($publicRoutes as $name) {
            $links = [];
            foreach ($locales as $l) {
                $links[$l] = $this->generateUrl($name, ['_locale' => $l], 0);
            }
            foreach ($locales as $l) {
                $urls[] = [
                    'loc' => $links[$l],
                    'alternates' => $links,
                    'lastmod' => $today,
                    'changefreq' => $name === 'app_home' ? 'weekly' : 'monthly',
                    'priority' => $name === 'app_home' ? '1.0' : '0.5',
                ];
            }
        }

        $xml = $this->renderView('sitemap.xml.twig', ['urls' => $urls]);

        return new Response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }
}
