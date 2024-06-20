<?php

namespace App\Tests\Service;

use App\Tests\Factory\TaskFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Security;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use App\Tests\Factory\UserFactory;
use App\Service\TaskAuthorizationService;

class TaskAuthorizationServiceTest extends WebTestCase
{
    use ResetDatabase, Factories;

    public function testCanDeleteReturnsFalseIfTheLoggedUserIsNotTheTaskCreator(): void
    {
        $userAdmin = UserFactory::createOne([
            'username' => 'userAdmin',
            'roles' => ['ROLE_ADMIN']
        ]);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($userAdmin->object());

        $user = UserFactory::createOne([
            'username' => 'otherUser',
            'roles' => ['ROLE_USER']
        ]);

        $task = TaskFactory::createOne([
            'title' => 'Titre de la tâche',
            'content' => 'Contenu de la tâche', 
            'user' => $user
        ]);

        $taskAutorizationService = new TaskAuthorizationService($security);

        $this->assertFalse($taskAutorizationService->canDelete($task->object()));
    }

    public function testCanDeleteReturnsFalseIfTheLoggedUserIsNotAdminAndTaskUserIsAnonyme(): void
    {
        $loggedUser = UserFactory::createOne([
            'username' => 'user',
            'roles' => ['ROLE_USER']
        ]);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($loggedUser->object());

        $user = UserFactory::createOne([
            'username' => 'anonyme',
            'roles' => ['ROLE_USER']
        ]);

        $task = TaskFactory::createOne([
            'title' => 'Titre de la tâche',
            'content' => 'Contenu de la tâche', 
            'user' => $user
        ]);

        $taskAutorizationService = new TaskAuthorizationService($security);

        $this->assertFalse($taskAutorizationService->canDelete($task->object()));
    }

    public function testCanDeleteReturnsTrueIfTheLoggedUserIsTheTaskCreator(): void
    {
        $loggedUser = UserFactory::createOne([
            'username' => 'user',
            'roles' => ['ROLE_USER']
        ]);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($loggedUser->object());

        $task = TaskFactory::createOne([
            'title' => 'Titre de la tâche',
            'content' => 'Contenu de la tâche', 
            'user' => $loggedUser
        ]);

        $taskAutorizationService = new TaskAuthorizationService($security);

        $this->assertTrue($taskAutorizationService->canDelete($task->object()));
    }
}
