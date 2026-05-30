<?php

namespace App\Repository;

use App\Entity\Household;
use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /** @return Message[] */
    public function findByHousehold(Household $household, int $limit = 50): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.household = :h')
            ->setParameter('h', $household)
            ->orderBy('m.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
