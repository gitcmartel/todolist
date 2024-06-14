<?php

namespace App\Tests\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AuthenticationEntryPointTest extends WebTestCase
{

    public function testStartReturnsLoginPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/users');

        // Controls that there is a redirection to the login page
        $this->assertResponseRedirects('/login', 302);
    }
}
