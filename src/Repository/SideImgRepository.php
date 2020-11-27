<?php

namespace App\Repository;

use App\Entity\SideImg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SideImg|null find($id, $lockMode = null, $lockVersion = null)
 * @method SideImg|null findOneBy(array $criteria, array $orderBy = null)
 * @method SideImg[]    findAll()
 * @method SideImg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SideImgRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SideImg::class);
    }


    public function getRandom()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('RAND ()')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()[0];
    }

    // /**
    //  * @return SideImg[] Returns an array of SideImg objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SideImg
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
