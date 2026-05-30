<?php

namespace App\Repository;

use App\Entity\ChoreAssignment;
use App\Entity\Household;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChoreAssignment>
 */
class ChoreAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChoreAssignment::class);
    }

    /** @return ChoreAssignment[] */
    public function findForWeek(Household $household, int $year, int $week): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.household = :h')
            ->andWhere('c.year = :y')
            ->andWhere('c.weekNumber = :w')
            ->setParameter('h', $household)
            ->setParameter('y', $year)
            ->setParameter('w', $week)
            ->orderBy('c.day', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
