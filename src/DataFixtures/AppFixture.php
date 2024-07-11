<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
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
        // Creation of 3 users

        $user = new User();
        $user->setUsername('usertest');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'passwordTest!1'));
        $user->setEmail('usertest@test.fr');
        $user->setRoles(['ROLE_USER']);
        $this->addReference('usertest', $user);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('useradmintest');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'passwordTest!1'));
        $user->setEmail('useradmintest@test.fr');
        $user->setRoles(['ROLE_ADMIN']);
        $this->addReference('useradmintest', $user);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('anonyme');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'passwordTest!1'));
        $user->setEmail('anonyme@test.fr');
        $user->setRoles(['ROLE_USER']);
        $this->addReference('anonyme', $user);
        $manager->persist($user);

        // Creation of 5 tasks

        $task = new Task();
        $task->setTitle('Développement fonctionnalité ajout d\'une tâche');
        $task->setCreatedAt(new \DateTime());
        $task->setIsDone(false);
        $task->setContent('Erat autem diritatis eius hoc quoque indicium nec obscurum nec latens, quod ludicris cruentis delectabatur et in circo sex vel septem aliquotiens vetitis certaminibus pugilum vicissim se concidentium perfusorumque sanguine specie ut lucratus ingentia laetabatur.');
        $user = $this->getReference('useradmintest');
        $task->setUser($user);
        $manager->persist($task);

        $task = new Task();
        $task->setTitle('Développement fonctionnalité login');
        $task->setCreatedAt(new \DateTime());
        $task->setIsDone(false);
        $task->setContent('Illud tamen clausos vehementer angebat quod captis navigiis, quae frumenta vehebant per flumen, Isauri quidem alimentorum copiis adfluebant, ipsi vero solitarum rerum cibos iam consumendo inediae propinquantis aerumnas exitialis horrebant.');
        $user = $this->getReference('usertest');
        $task->setUser($user);
        $manager->persist($task);

        $task = new Task();
        $task->setTitle('Rédaction de la documentation');
        $task->setCreatedAt(new \DateTime());
        $task->setIsDone(false);
        $task->setContent('Post quorum necem nihilo lenius ferociens Gallus ut leo cadaveribus pastus multa huius modi scrutabatur. quae singula narrare non refert, me professione modum, quod evitandum est, excedamus.');
        $user = $this->getReference('usertest');
        $task->setUser($user);
        $manager->persist($task);

        $task = new Task();
        $task->setTitle('Ecriture des tests fonctionnels');
        $task->setCreatedAt(new \DateTime());
        $task->setIsDone(false);
        $task->setContent('Accedebant enim eius asperitati, ubi inminuta vel laesa amplitudo imperii dicebatur, et iracundae suspicionum quantitati proximorum cruentae blanditiae exaggerantium incidentia et dolere inpendio simulantium, si principis periclitetur vita, a cuius salute velut filo pendere statum orbis terrarum fictis vocibus exclamabant.');
        $user = $this->getReference('anonyme');
        $task->setUser($user);
        $manager->persist($task);

        $task = new Task();
        $task->setTitle('Ecriture des tests unitaires');
        $task->setCreatedAt(new \DateTime());
        $task->setIsDone(false);
        $task->setContent('Primi igitur omnium statuuntur Epigonus et Eusebius ob nominum gentilitatem oppressi. praediximus enim Montium sub ipso vivendi termino his vocabulis appellatos fabricarum culpasse tribunos ut adminicula futurae molitioni pollicitos.');
        $user = $this->getReference('anonyme');
        $task->setUser($user);
        $manager->persist($task);

        $manager->flush();
    }
}
