<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\TaskFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TaskControllerTest extends WebTestCase
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
    
    public function testListActionReturnsView()
    {
        $tasks = TaskFactory::createMany(2);

        $crawler = $this->client->request('GET', '/tasks');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateActionReturnsRedirectHttpCode()
    {
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateActionReturnsView()
    {
        UserFactory::createOne([
            'username' => 'usertest',
            'roles' => ['ROLE_USER']
        ]);

        $userRepository = static::getContainer()->get(UserRepository::class);

        // Retrieve the test user
        $testUser = $userRepository->findOneByUsername('usertest');

        // Simulate $testUser being logged in
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Titre', $crawler->filter('label')->text());
    }

    public function testCreateActionReturnsTasksListAndContainsData()
    {       
        UserFactory::createOne([
            'username' => 'usertest',
            'roles' => ['ROLE_USER']
        ]);

        // Simulate $testUser being logged in
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Retrieve the test user
        $testUser = $userRepository->findOneByUsername('usertest');

        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/tasks/create');

        // Select the button
        $buttonCrawlerNode = $crawler->selectButton('submit');

        // Retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // Set values to the form object and submit it
        $this->client->submit($form, [
            'task_form[title]' => 'Titre de la tâche',
            'task_form[content]' => 'Contenu de la tâche',
            'task_form[isDone]' => '1',
        ]);

        // Controls that there is a redirection to the tasks list page
        $this->assertResponseRedirects('/tasks', 302);
        $crawler = $this->client->followRedirect();

        // Filter the 'a' elements to find those who contains the desired string
        $filteredLinks = $crawler->filter('a')->reduce(function ($node) {
            return strpos($node->text(), 'Titre de la tâche') !== false;
        });

        $this->assertGreaterThan(0, count($filteredLinks));
    }

    public function testEditActionReturnsView()
    {
        $user = UserFactory::createOne([
            'username' => 'usertest',
            'roles' => ['ROLE_USER']
        ]);

        $task = TaskFactory::createOne([
            'title' => 'Titre de la tâche',
            'content' => 'Contenu de la tâche', 
            'user' => $user
        ]);
        
        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Titre', $crawler->filter('label')->text());
    }

    public function testEditActionReturnsTasksListAndContainsModifiedData()
    {
        $user =UserFactory::createOne([
            'username' => 'usertest',
            'roles' => ['ROLE_USER']
        ]);

        $task = TaskFactory::createOne([
            'title' => 'Titre de la tâche',
            'content' => 'Contenu de la tâche', 
            'user' => $user
        ]);

        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');

        // Select the button
        $buttonCrawlerNode = $crawler->selectButton('submit');

        // Retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // Set values to the form object and submit it
        $this->client->submit($form, [
            'task_form[title]' => 'Titre de la tâche modifié',
            'task_form[content]' => 'Contenu de la tâche modifié',
            'task_form[isDone]' => '1',
        ]);

        // Controls that there is a redirection to the tasks list page
        $this->assertResponseRedirects('/tasks', 302);
        $crawler = $this->client->followRedirect();

        // Filter the 'a' elements to find those who contains the desired string
        $filteredLinks = $crawler->filter('a')->reduce(function ($node) {
            return strpos($node->text(), 'Titre de la tâche modifié') !== false;
        });

        $this->assertGreaterThan(0, count($filteredLinks));
    }

    public function testToggleTaskActionReturnsTasksListAndButtonWithMarkAsDoneLabel()
    {
        $user =UserFactory::createOne([
            'username' => 'usertest',
            'roles' => ['ROLE_USER']
        ]);

        $task = TaskFactory::createOne([
            'title' => 'Titre de la tâche',
            'content' => 'Contenu de la tâche', 
            'user' => $user
        ]);

        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/toggle');

        // Controls that there is a redirection to the tasks list page
        $this->assertResponseRedirects('/tasks', 302);
        $crawler = $this->client->followRedirect();

        // Filter the 'button' elements to find those who contains the desired string
        $filteredButtons = $crawler->filter('button')->reduce(function ($node) {
            return strpos($node->text(), 'Marquer non terminée') !== false;
        });

        $this->assertGreaterThan(0, count($filteredButtons));
    }

    public function testDeleteActionReturnsEmptyTasksList()
    {
        $user = UserFactory::createOne([
            'username' => 'usertest',
            'roles' => ['ROLE_USER']
        ]);

        TaskFactory::createOne([
            'title' => 'Titre de la tâche',
            'content' => 'Contenu de la tâche', 
            'user' => $user
        ]);

        // Simulate $testUser being logged in
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Retrieve the test user
        $testUser = $userRepository->findOneByUsername('usertest');

        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/tasks');

        // Select the button
        $buttonCrawlerNode = $crawler->selectButton('deleteSubmit');

        // Retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // Set values to the form object and submit it
        $this->client->submit($form);
        
        // Controls that there is a redirection to the tasks list page
        $this->assertResponseRedirects('/tasks', 302);
        $crawler = $this->client->followRedirect();

        // Filter the 'a' elements to find those who contains the desired string
        $filteredLinks = $crawler->filter('a')->reduce(function ($node) {
            return strpos($node->text(), 'Titre de la tâche') !== false;
        });

        // Given that the unique task as been deleted there should not be any title in the tasks list page
        $this->assertEquals(0, count($filteredLinks));
    }

    public function testDeleteActionUnautorizedReturnsFullTasksList()
    {
        $userCreator = UserFactory::createOne([
            'username' => 'userCreator',
            'roles' => ['ROLE_USER']
        ]);

        $userActor = UserFactory::createOne([
            'username' => 'userActor',
            'roles' => ['ROLE_USER']
        ]);

        TaskFactory::createOne([
            'title' => 'Titre de la tâche',
            'content' => 'Contenu de la tâche', 
            'user' => $userCreator
        ]);

        // Simulate $userActor being logged in
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Retrieve the actor user
        $userActor = $userRepository->findOneByUsername('userActor');
        
        $this->client->loginUser($userActor);

        $crawler = $this->client->request('GET', '/tasks');

        // Select the button
        $buttonCrawlerNode = $crawler->selectButton('deleteSubmit');

        // Retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // Set values to the form object and submit it
        $this->client->submit($form);
        
        // Controls that there is a redirection to the tasks list page
        $this->assertResponseRedirects('/tasks', 302);
        $crawler = $this->client->followRedirect();
        
        // Filter the 'a' elements to find those who contains the desired string
        $filteredLinks = $crawler->filter('a')->reduce(function ($node) {
            return strpos($node->text(), 'Titre de la tâche') === true;
        });
        
        // Given that the unique task as been deleted there should not be any title in the tasks list page
        $this->assertGreaterThan(0, count($filteredLinks));
    }
}