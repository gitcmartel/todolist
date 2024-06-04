<?php

namespace App\Tests\Controller;


use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\TaskFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TaskControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    public function testListActionReturnsView()
    {
        $client = static::createClient();

        $tasks = TaskFactory::createMany(2);

        $crawler = $client->request('GET', '/tasks');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
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

        UserFactory::createOne([
            'username' => 'usertest',
            'roles' => ['ROLE_USER']
        ]);

        $userRepository = static::getContainer()->get(UserRepository::class);

        // Retrieve the test user
        $testUser = $userRepository->findOneByUsername('usertest');

        // Simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks/create');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Titre', $crawler->filter('label')->text());
    }

    public function testCreateActionReturnsTasksListAndContainsData()
    {
        $client = static::createClient();
        
        UserFactory::createOne([
            'username' => 'usertest',
            'roles' => ['ROLE_USER']
        ]);

        // Simulate $testUser being logged in
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Retrieve the test user
        $testUser = $userRepository->findOneByUsername('usertest');

        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks/create');

        // Select the button
        $buttonCrawlerNode = $crawler->selectButton('submit');

        // Retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // Set values to the form object and submit it
        $client->submit($form, [
            'task_form[title]' => 'Titre de la tâche',
            'task_form[content]' => 'Contenu de la tâche',
            'task_form[isDone]' => '1',
        ]);

        // Controls that there is a redirection to the tasks list page
        $this->assertResponseRedirects('/tasks', 302);
        $crawler = $client->followRedirect();

        // Filter the 'a' elements to find those who contains the desired string
        $filteredLinks = $crawler->filter('a')->reduce(function ($node) {
            return strpos($node->text(), 'Titre de la tâche') !== false;
        });

        $this->assertGreaterThan(0, count($filteredLinks));
    }

    public function testEditActionReturnsView()
    {
        $client = static::createClient();

        $user = UserFactory::createOne([
            'username' => 'usertest',
            'roles' => ['ROLE_USER']
        ]);

        TaskFactory::createOne([
            'title' => 'Titre de la tâche',
            'content' => 'Contenu de la tâche', 
            'user' => $user
        ]);

        $crawler = $client->request('GET', '/tasks/1/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Titre', $crawler->filter('label')->text());
    }

    public function testEditActionReturnsTasksListAndContainsModifiedData()
    {
        $client = static::createClient();

        $user =UserFactory::createOne([
            'username' => 'usertest',
            'roles' => ['ROLE_USER']
        ]);

        TaskFactory::createOne([
            'title' => 'Titre de la tâche',
            'content' => 'Contenu de la tâche', 
            'user' => $user
        ]);

        $crawler = $client->request('GET', '/tasks/1/edit');

        // Select the button
        $buttonCrawlerNode = $crawler->selectButton('submit');

        // Retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // Set values to the form object and submit it
        $client->submit($form, [
            'task_form[title]' => 'Titre de la tâche modifié',
            'task_form[content]' => 'Contenu de la tâche modifié',
            'task_form[isDone]' => '1',
        ]);

        // Controls that there is a redirection to the tasks list page
        $this->assertResponseRedirects('/tasks', 302);
        $crawler = $client->followRedirect();

        // Filter the 'a' elements to find those who contains the desired string
        $filteredLinks = $crawler->filter('a')->reduce(function ($node) {
            return strpos($node->text(), 'Titre de la tâche modifié') !== false;
        });

        $this->assertGreaterThan(0, count($filteredLinks));
    }
}