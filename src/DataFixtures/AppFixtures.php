<?php

namespace App\DataFixtures;

use App\Entity\ChoreAssignment;
use App\Entity\Expense;
use App\Entity\Household;
use App\Entity\Message;
use App\Entity\Payment;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $owner = (new User())
            ->setEmail('owner@coloc.local')
            ->setFirstName('Marc')
            ->setLastName('Dubois')
            ->setRoles(['ROLE_OWNER']);
        $owner->setPassword($this->hasher->hashPassword($owner, 'owner123'));
        $manager->persist($owner);

        $house = (new Household())
            ->setName('La Joyeuse Coloc de Paris')
            ->setAddress("12 Rue de l'Ambre, 75011 Paris")
            ->setOwner($owner)
            ->setMonthlyCharges('600.00');
        $manager->persist($house);

        $tenant1 = (new User())
            ->setEmail('sophie@coloc.local')
            ->setFirstName('Sophie')
            ->setLastName('Martin')
            ->setRoles(['ROLE_TENANT'])
            ->setHousehold($house)
            ->setTantieme('50.00')
            ->setMonthlyRent('450.00');
        $tenant1->setPassword($this->hasher->hashPassword($tenant1, 'tenant123'));
        $manager->persist($tenant1);

        $tenant2 = (new User())
            ->setEmail('julien@coloc.local')
            ->setFirstName('Julien')
            ->setLastName('Bernard')
            ->setRoles(['ROLE_TENANT'])
            ->setHousehold($house)
            ->setTantieme('50.00')
            ->setMonthlyRent('450.00');
        $tenant2->setPassword($this->hasher->hashPassword($tenant2, 'tenant123'));
        $manager->persist($tenant2);

        $tasksData = [
            ['Vaisselle', 'Laver et ranger la vaisselle du soir', 10],
            ['Ménage Salon', "Aspirateur et nettoyage de la table basse", 25],
            ['Sortir les poubelles', 'Tri sélectif et bac vert', 5],
            ['Nettoyage salle de bain', "Lavabo, douche, miroir, sol", 30],
            ['Courses communes', 'Liste partagée et tickets gardés', 20],
        ];
        $tasks = [];
        foreach ($tasksData as [$title, $desc, $pts]) {
            $t = (new Task())->setTitle($title)->setDescription($desc)->setPointsValue($pts);
            $manager->persist($t);
            $tasks[] = $t;
        }

        $period = date('Y-m');
        $expenses = [
            ['water', 'Veolia', '95.40'],
            ['electricity', 'EDF Bleu Ciel', '180.50'],
            ['internet', 'Free Fibre', '39.99'],
            ['taxes', "Taxe d'habitation (mensualisée)", '120.00'],
        ];
        foreach ($expenses as [$cat, $label, $amount]) {
            $e = (new Expense())
                ->setHousehold($house)
                ->setCategory($cat)
                ->setLabel($label)
                ->setAmount($amount)
                ->setPeriod($period);
            $manager->persist($e);
        }

        $lastMonth = (new \DateTimeImmutable('first day of last month'))->format('Y-m');
        foreach ([$tenant1, $tenant2] as $t) {
            $p = (new Payment())
                ->setTenant($t)
                ->setHousehold($house)
                ->setPeriod($lastMonth)
                ->setRentAmount($t->getMonthlyRent())
                ->setChargesAmount('218.00')
                ->setStatus(Payment::STATUS_PAID)
                ->setPaidAt(new \DateTimeImmutable($lastMonth . '-05'));
            $manager->persist($p);
        }

        $year = (int) date('o');
        $week = (int) date('W');
        $plan = [
            ['monday',    0, $tenant1],
            ['wednesday', 1, $tenant2],
            ['friday',    2, $tenant1],
            ['saturday',  3, $tenant2],
        ];
        foreach ($plan as [$day, $tIdx, $assignee]) {
            $a = (new ChoreAssignment())
                ->setHousehold($house)
                ->setTask($tasks[$tIdx])
                ->setAssignedTo($assignee)
                ->setDay($day)
                ->setYear($year)
                ->setWeekNumber($week);
            $manager->persist($a);
        }

        $welcome = (new Message())
            ->setHousehold($house)
            ->setAuthor($owner)
            ->setContent("Bienvenue dans la coloc ! N'hésitez pas à utiliser ce fil pour tout signaler.");
        $manager->persist($welcome);

        $reply = (new Message())
            ->setHousehold($house)
            ->setAuthor($tenant1)
            ->setContent("Hello tout le monde 👋 Je m'occupe de la vaisselle lundi !");
        $manager->persist($reply);

        $manager->flush();
    }
}
