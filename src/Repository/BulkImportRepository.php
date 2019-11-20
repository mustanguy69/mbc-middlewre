<?php

namespace App\Repository;

use App\Entity\BulkImport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BulkImport|null find($id, $lockMode = null, $lockVersion = null)
 * @method BulkImport|null findOneBy(array $criteria, array $orderBy = null)
 * @method BulkImport[]    findAll()
 * @method BulkImport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BulkImportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BulkImport::class);
    }

    // /**
    //  * @return BulkImport[] Returns an array of BulkImport objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BulkImport
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
