<?php

namespace App\Repository;

use App\Entity\Filters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Filters|null find($id, $lockMode = null, $lockVersion = null)
 * @method Filters|null findOneBy(array $criteria, array $orderBy = null)
 * @method Filters[]    findAll()
 * @method Filters[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FiltersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Filters::class);
    }

    // /**
    //  * @return Filters[] Returns an array of Filters objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Filters
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
