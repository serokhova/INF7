<?php

namespace App\Repository;

use App\Entity\Household;
use App\Entity\Payment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /** @return Payment[] */
    public function findByTenant(User $tenant): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.tenant = :t')
            ->setParameter('t', $tenant)
            ->orderBy('p.period', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return Payment[] */
    public function findByHousehold(Household $household): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.household = :h')
            ->setParameter('h', $household)
            ->orderBy('p.period', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function totalReceivedForHousehold(Household $household, string $period): float
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COALESCE(SUM(p.rentAmount + p.chargesAmount), 0)')
            ->andWhere('p.household = :h')
            ->andWhere('p.period = :period')
            ->andWhere('p.status = :paid')
            ->setParameter('h', $household)
            ->setParameter('period', $period)
            ->setParameter('paid', Payment::STATUS_PAID);

        return (float) $qb->getQuery()->getSingleScalarResult();
    }
}
