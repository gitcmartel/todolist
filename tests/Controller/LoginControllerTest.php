<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $authenticationUtils = $this->createMock(AuthenticationUtils::class);
        $authenticationUtils->method('getLastAuthenticationError')
            ->willReturn(null);
        $authenticationUtils->method('getLastUsername')
            ->willReturn('username_test');
        self::getContainer()->set(AuthenticationUtils::class, $authenticationUtils);

        $security = $this->createMock(Security::class);
        $security->method('getUser')
            ->willReturn(null);
        self::getContainer()->set(Security::class, $security);
        
        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Nom d\'utilisateur', $crawler->filter('label')->text());
    }

    public function testUserIsLoggedOutIfAuthenticated()
    {
        $client = static::createClient();

        $authenticationUtils = $this->createMock(AuthenticationUtils::class);
        $authenticationUtils->method('getLastAuthenticationError')
            ->willReturn(null);
        $authenticationUtils->method('getLastUsername')
            ->willReturn('username_test');
        self::getContainer()->set(AuthenticationUtils::class, $authenticationUtils);

        $security = $this->createMock(Security::class);
        $user = $this->createMock(UserInterface::class);
        $security->method('getUser')->willReturn($user);

        $security->method('logout')->willReturn(new Response());
        $security->expects(self::once())->method('logout');
        self::getContainer()->set(Security::class, $security);

        $client->request('GET', '/login');
    }

    public function testDataIsPassedToView()
    {
        $client = static::createClient();

        $authenticationUtils = $this->createMock(AuthenticationUtils::class);
        $authenticationUtils->method('getLastAuthenticationError')
            ->willReturn(null);
        $authenticationUtils->method('getLastUsername')
            ->willReturn('testUsername');
        self::getContainer()->set(AuthenticationUtils::class, $authenticationUtils);

        $security = $this->createMock(Security::class);
        $security->method('getUser')
            ->willReturn(null);
        self::getContainer()->set(Security::class, $security);

        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('#username'));
        $this->assertStringContainsString('testUsername', $crawler->filter('#username')->attr('value'));
    }
}