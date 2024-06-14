<?php

namespace App\Tests\Tests\Security;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use App\Tests\Factory\UserFactory;

class AccessDeniedHandlerTest extends WebTestCase
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
            'roles' => ['ROLE_USER']
        ]);

        $testUser = $this->userRepository->findOneByUsername('usertest');
        $this->client->loginUser($testUser);
    }

    public function testHandleReturnsDeniedPage(): void
    {
        $this->createAndLoginTestUser();

        $this->client->request('GET', '/users');

        // Controls that there is a redirection to the access denied
        $this->assertResponseRedirects('/security/denied', 302);
    }
}
