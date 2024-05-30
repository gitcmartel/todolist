<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $authenticationUtils = $this->createMock(AuthenticationUtils::class);
        $authenticationUtils->method('getLastAuthenticationError')
            ->willReturn(null);
        $authenticationUtils->method('getLastUsername')
            ->willReturn('username_test');

        $security = $this->createMock(Security::class);
        $security->method('getUser')
            ->willReturn(null);

        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Nom d\'utilisateur', $crawler->filter('label')->text());
    }

    public function testUserIsLoggedOutIfAuthenticated()
    {
        $authenticationUtils = $this->createMock(AuthenticationUtils::class);
        $authenticationUtils->method('getLastAuthenticationError')
            ->willReturn(null);
        $authenticationUtils->method('getLastUsername')
            ->willReturn('username_test');

        $security = $this->createMock(Security::class);
        $user = $this->createMock(UserInterface::class);
        $security->method('getUser')->willReturn($user);
        $security->expects(self::once())->method('logout');

        $client = static::createClient();
        $client->request('GET', '/login');
    }

    public function testDataIsPassedToView()
    {
        $authenticationUtils = $this->createMock(AuthenticationUtils::class);
        $authenticationUtils->method('getLastAuthenticationError')
            ->willReturn(null);
        $authenticationUtils->method('getLastUsername')
            ->willReturn('testUsername');

        $security = $this->createMock(Security::class);
        $security->method('getUser')
            ->willReturn(null);

        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('#username'));
        $this->assertStringContainsString('testUsername', $crawler->filter('#username')->attr('value'));
        $this->assertEmpty($crawler->filter('.error')->text());
    }
}