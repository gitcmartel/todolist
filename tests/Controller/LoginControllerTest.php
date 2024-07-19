<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use App\Tests\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class LoginControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    private $client;
    private $userRepository;

    protected function setUp(): void 
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    private function createAndLoginTestUser()
    {
        UserFactory::createOne([
            'username' => 'usertest',
            'roles' => ['ROLE_ADMIN']
        ]);

        $testUser = $this->userRepository->findOneByUsername('usertest');
        $this->client->loginUser($testUser);
    }

    public function testIndex()
    {
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
        
        $crawler = $this->client->request('GET', '/login');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Nom d\'utilisateur', $crawler->filter('label')->text());
    }

    public function testUserIsLoggedOutIfAuthenticated()
    {
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

        $this->client->request('GET', '/login');
    }

    public function testDataIsPassedToView()
    {
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

        $crawler = $this->client->request('GET', '/login');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('#username'));
        $this->assertStringContainsString('testUsername', $crawler->filter('#username')->attr('value'));
    }

    public function testLogout()
    {
        $security = static::getContainer()->get(Security::class);
        $this->createAndLoginTestUser();
        $this->assertEquals('usertest', $security->getUser()->getUsername());

        $this->client->request('GET', '/logout');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertNull($security->getUser());
    }
}