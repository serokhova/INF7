<?php

namespace App\Repository;

use App\Entity\Expense;
use App\Entity\Household;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    /** @return Expense[] */
    public function findByHousehold(Household $household, ?string $period = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.household = :h')
            ->setParameter('h', $household)
            ->orderBy('e.period', 'DESC')
            ->addOrderBy('e.id', 'DESC');

        if ($period !== null) {
            $qb->andWhere('e.period = :p')->setParameter('p', $period);
        }

        return $qb->getQuery()->getResult();
    }

    /** @return array<string, float> */
    public function totalsByCategory(Household $household, string $period): array
    {
        $rows = $this->createQueryBuilder('e')
            ->select('e.category AS category, SUM(e.amount) AS total')
            ->andWhere('e.household = :h')
            ->andWhere('e.period = :p')
            ->setParameter('h', $household)
            ->setParameter('p', $period)
            ->groupBy('e.category')
            ->getQuery()
            ->getResult();

        $out = [];
        foreach ($rows as $r) {
            $out[$r['category']] = (float) $r['total'];
        }
        return $out;
    }
}
