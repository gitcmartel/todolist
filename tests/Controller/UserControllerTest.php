<?php

namespace App\Tests\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use App\Tests\Factory\UserFactory;

class UserControllerTest extends WebTestCase
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

    public function testListReturnsView()
    {
        $this->createAndLoginTestUser();

        UserFactory::createMany(2);

        $this->client->request('GET', '/users');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateReturnsUsersListAndContainsData()
    {
        $this->createAndLoginTestUser();

        $crawler = $this->client->request('GET', '/users/create');

        // Select the button
        $buttonCrawlerNode = $crawler->selectButton('submit');

        // Retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // Set values to the form object and submit it
        $this->client->submit($form, [
            'user_form[username]' => 'newUser',
            'user_form[password][first]' => 'p@sswordTest24',
            'user_form[password][second]' => 'p@sswordTest24',
            'user_form[email]' => 'newuser@gmail.com',
            'user_form[role]' => 'ROLE_USER'
        ]);

        // Controls that there is a redirection to the users list page
        $this->assertResponseRedirects('/users', 302);
        $crawler = $this->client->followRedirect();

        // Filter the 'td' elements to find those who contains the desired string
        $filteredLinks = $crawler->filter('td')->reduce(function ($node) {
            return strpos($node->text(), 'newUser') !== false;
        });

        $this->assertGreaterThan(0, count($filteredLinks));
    }

    public function testEditActionReturnsView()
    {
        $this->createAndLoginTestUser();

        UserFactory::createOne([
            'username' => 'newUser',
            'roles' => ['ROLE_USER']
        ]);

        $crawler = $this->client->request('GET', '/users/2/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Filter the 'input' elements to find those who contains the desired string
        $filteredLinks = $crawler->filter('input')->reduce(function ($node) {
            return strpos($node->attr('value'), 'newUser') !== false;
        });

        $this->assertGreaterThan(0, count($filteredLinks));
    }

    public function testEditReturnsUsersListAndContainsModifiedData()
    {
        $this->createAndLoginTestUser();

        UserFactory::createOne([
            'username' => 'newUser',
            'roles' => ['ROLE_USER']
        ]);

        $crawler = $this->client->request('GET', '/users/2/edit');

        // Select the button
        $buttonCrawlerNode = $crawler->selectButton('submit');

        // Retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // Set values to the form object and submit it
        $this->client->submit($form, [
            'user_form[username]' => 'newUser1',
            'user_form[password][first]' => 'p@sswordTest24',
            'user_form[password][second]' => 'p@sswordTest24',
            'user_form[email]' => 'newuser1@gmail.com',
            'user_form[role]' => 'ROLE_USER'
        ]);

        // Controls that there is a redirection to the tasks list page
        $this->assertResponseRedirects('/users', 302);
        $crawler = $this->client->followRedirect();

        // Filter the 'a' elements to find those who contains the desired string
        $filteredLinks = $crawler->filter('td')->reduce(function ($node) {
            return strpos($node->text(), 'newUser1') !== false;
        });

        $this->assertGreaterThan(0, count($filteredLinks));
    }
}
