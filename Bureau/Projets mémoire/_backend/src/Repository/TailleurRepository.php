<?php

namespace App\Repository;

use App\Entity\Tailleur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tailleur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tailleur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tailleur[]    findAll()
 * @method Tailleur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TailleurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tailleur::class);
    }

    // /**
    //  * @return Tailleur[] Returns an array of Tailleur objects
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
    public function findOneBySomeField($value): ?Tailleur
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
