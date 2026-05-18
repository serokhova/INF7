<?php

namespace App\DataFixtures;

use App\Entity\Household;
use App\Entity\User;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $owner = new User();
        $owner->setEmail('owner@coloc.local')
              ->setFirstName('Marc')
              ->setLastName('Dubois')
              ->setRoles(['ROLE_OWNER'])
              ->setPassword($this->hasher->hashPassword($owner, 'owner123'));
        $manager->persist($owner);

        $house = new Household();
        $house->setName('La Joyeuse Coloc de Paris')
              ->setAddress('12 Rue de l\'Ambre, 75011 Paris')
              ->setOwner($owner);
        $manager->persist($house);

        $tenant1 = new User();
        $tenant1->setEmail('sophie@coloc.local')
                ->setFirstName('Sophie')
                ->setLastName('Martin')
                ->setRoles(['ROLE_TENANT'])
                ->setHousehold($house)
                ->setPassword($this->hasher->hashPassword($tenant1, 'tenant123'));
        $manager->persist($tenant1);

        $tenant2 = new User();
        $tenant2->setEmail('julien@coloc.local')
                ->setFirstName('Julien')
                ->setLastName('Bernard')
                ->setRoles(['ROLE_TENANT'])
                ->setHousehold($house)
                ->setPassword($this->hasher->hashPassword($tenant2, 'tenant123'));
        $manager->persist($tenant2);

        $task1 = new Task();
        $task1->setTitle('Vaisselle')->setDescription('Laver et ranger la vaisselle du soir')->setPointsValue(10);
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setTitle('Ménage Salon')->setDescription('Passer l\'aspirateur et nettoyer la table basse')->setPointsValue(25);
        $manager->persist($task2);

        $manager->flush();
    }
}
