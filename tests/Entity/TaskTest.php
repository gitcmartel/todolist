<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase 
{
    protected Task $task;

    protected function setUp(): void
    {
        $this->task = new Task();
    }

    public function testIdGetterAndSetter()
    {
        $this->task->setId(10);

        $this->assertEquals(10, $this->task->getId());
    }

    public function testCreatedAtGetterAndSetter()
    {
        $date = new \DateTime('2024-01-01 00:00:00');
        $this->task->setCreatedAt($date);

        $this->assertEquals($date, $this->task->getCreatedAt());
    }

    public function testTitleGetterAndSetter()
    {
        $this->task->setTitle('Titre');

        $this->assertEquals('Titre', $this->task->getTitle());
    }

    public function testContentGetterAndSetter()
    {
        $this->task->setContent('Contenu');

        $this->assertEquals('Contenu', $this->task->getContent());
    }

    public function testIsDoneGetterAndSetter()
    {
        $this->task->setIsDone(true);
        $this->assertTrue($this->task->getIsDone());
        $this->task->setIsDone(false);
        $this->assertFalse($this->task->getIsDone());
    }

    public function testUserGetterAndSetter()
    {
        $user = new User();
        $this->task->setUser($user);
        $this->assertSame($user, $this->task->getUser());
    }
}