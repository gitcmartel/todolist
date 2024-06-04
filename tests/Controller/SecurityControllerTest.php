<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testAccessDeniedReturnsView()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/security/denied');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Accès Refusé', $crawler->filter('h1')->text());
    }
}