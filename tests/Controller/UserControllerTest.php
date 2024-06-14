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
    public function testListReturnsView()
    {
        $client = static::createClient();

        UserFactory::createOne([
            'username' => 'usertest',
            'roles' => ['ROLE_ADMIN']
        ]);

        $userRepository = static::getContainer()->get(UserRepository::class);

        // Retrieve the test user
        $testUser = $userRepository->findOneByUsername('usertest');

        // Simulate $testUser being logged in
        $client->loginUser($testUser);

        UserFactory::createMany(2);

        $client->request('GET', '/users');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateReturnsUsersListAndContainsData()
    {
        $client = static::createClient();
        
        UserFactory::createOne([
            'username' => 'usertest',
            'roles' => ['ROLE_ADMIN']
        ]);

        // Simulate $testUser being logged in
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Retrieve the test user
        $testUser = $userRepository->findOneByUsername('usertest');

        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/users/create');

        // Select the button
        $buttonCrawlerNode = $crawler->selectButton('submit');

        // Retrieve the Form object for the form belonging to this button
        $form = $buttonCrawlerNode->form();

        // Set values to the form object and submit it
        $client->submit($form, [
            'user_form[username]' => 'newUser',
            'user_form[password][first]' => 'p@sswordTest24',
            'user_form[password][second]' => 'p@sswordTest24',
            'user_form[email]' => 'newuser@gmail.com',
            'user_form[role]' => 'ROLE_USER'
        ]);

        // Controls that there is a redirection to the users list page
        $this->assertResponseRedirects('/users', 302);
        $crawler = $client->followRedirect();

        // Filter the 'a' elements to find those who contains the desired string
        $filteredLinks = $crawler->filter('td')->reduce(function ($node) {
            return strpos($node->text(), 'newUser') !== false;
        });

        $this->assertGreaterThan(0, count($filteredLinks));
    }
}
