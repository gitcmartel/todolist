<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase 
{
    protected User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testIdGetterAndSetter()
    {
        $this->user->setId(10);

        $this->assertEquals(10, $this->user->getId());
    }

    public function testUsernameGetterAndSetter()
    {
        $this->user->setUsername('Christophe');

        $this->assertEquals('Christophe', $this->user->getUsername());
    }

    public function testPasswordGetterAndSetter()
    {
        $this->user->setPassword('passwordTest!0');

        $this->assertEquals('passwordTest!0', $this->user->getPassword());
    }

    public function testEmailGetterAndSetter()
    {
        $this->user->setEmail('test@orange.fr');

        $this->assertEquals('test@orange.fr', $this->user->getEmail());
    }

    public function testRoleGetterAndSetter()
    {
        $this->user->setRoles(['ROLE_USER']);

        $this->assertEquals(['ROLE_USER'], $this->user->getRoles());
    }

    public function testGetUserIdentifierReturnUsername()
    {
        $this->user->setUsername('Christophe');

        $this->assertEquals('Christophe', $this->user->getUserIdentifier());
    }

    public function testGetTasksReturnColletionOfTasks()
    {
        $task1 = new Task();
        $task2 = new Task();

        $tasks = new ArrayCollection();
        $tasks->add($task1);
        $tasks->add($task2);

        $this->user->addTask($task1);
        $this->user->addTask($task2);

        $result = $this->user->getTasks();

        $this->assertInstanceOf(ArrayCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals($tasks, $result);
    }

    public function testAddTaskIncreasesTaskCountByOne()
    {
        $task = new Task();
        $initialNumberOfTasks = count($this->user->getTasks());
        $this->user->addTask($task);

        $this->assertEquals(1, count($this->user->getTasks()) - $initialNumberOfTasks);
        $this->assertTrue($this->user->getTasks()->contains($task));
    }

    
    public function testRemoveTaskDecreasesTaskCountByOne()
    {
        $task = new Task();
        $this->user->addTask($task);

        $initialNumberOfTasks = count($this->user->getTasks());
        
        $this->user->removeTask($task);

        $this->assertEquals(1, $initialNumberOfTasks - count($this->user->getTasks()));
        $this->assertFalse($this->user->getTasks()->contains($task), "The task should not be in the tasks list after removal.");
    }
}