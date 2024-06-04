<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixture extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('usertest');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'passwordTest!1'));
        $user->setEmail('usertest@test.fr');
        $user->setRoles(["ROLE_USER"]);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('useradmintest');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'passwordTest!1'));
        $user->setEmail('useradmintest@test.fr');
        $user->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user);

        $manager->flush();
    }
}
