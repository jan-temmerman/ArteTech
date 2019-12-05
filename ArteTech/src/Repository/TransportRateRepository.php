<?php

namespace App\Repository;

use App\Entity\TransportRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TransportRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransportRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransportRate[]    findAll()
 * @method TransportRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransportRate::class);
    }

    // /**
    //  * @return TransportRate[] Returns an array of TransportRate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TransportRate
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
