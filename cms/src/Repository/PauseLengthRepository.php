<?php

namespace App\Repository;

use App\Entity\PauseLength;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PauseLength|null find($id, $lockMode = null, $lockVersion = null)
 * @method PauseLength|null findOneBy(array $criteria, array $orderBy = null)
 * @method PauseLength[]    findAll()
 * @method PauseLength[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PauseLengthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PauseLength::class);
    }

    // /**
    //  * @return PauseLength[] Returns an array of PauseLength objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PauseLength
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
