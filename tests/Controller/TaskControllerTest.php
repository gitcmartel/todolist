<?php

namespace App\Tests\Controller;


use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{


    /**
     * @dataProvider tasksProvider
     */
    public function testListActionReturnsView($tasks)
    {
        $client = static::createClient();

        $taskRepository = $this->createMock(TaskRepository::class);
        $taskRepository->method('findAll')
            ->willReturn($tasks);
        self::getContainer()->set(TaskRepository::class, $taskRepository);

        $crawler = $client->request('GET', '/tasks');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        //$this->assertStringContainsString('Accès Refusé', $crawler->filter('h1')->text());
    }

    public function testCreateActionReturnsRedirectHttpCode()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks/create');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testCreateActionReturnsView()
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        // Retrieve the test user
        $testUser = $userRepository->findOneByUsername('usertest');

        // Simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks/create');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Titre', $crawler->filter('label')->text());
    }
    
    public function tasksProvider()
    {
        $task1 = new Task();
        $task1->setId(1);
        $task1->setCreatedAt(new \DateTime);
        $task1->setTitle('Title test 1');
        $task1->setContent(('Task content 1'));
        $task1->setUser(new User());

        $task2 = new Task();
        $task2->setId(2);
        $task2->setCreatedAt(new \DateTime);
        $task2->setTitle('Title test 2');
        $task2->setContent(('Task content 2'));
        $task2->setUser(new User());

        $tasks = [$task1, $task2];
        yield[$tasks];
    }
}