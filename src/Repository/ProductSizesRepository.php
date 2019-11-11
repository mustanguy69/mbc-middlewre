<?php

namespace App\Repository;

use App\Entity\ProductSizes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductSizes|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductSizes|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductSizes[]    findAll()
 * @method ProductSizes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductSizesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductSizes::class);
    }

    // /**
    //  * @return ProductSizes[] Returns an array of ProductSizes objects
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
    public function findOneBySomeField($value): ?ProductSizes
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
