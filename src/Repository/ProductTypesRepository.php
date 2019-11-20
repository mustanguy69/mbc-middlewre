<?php

namespace App\Repository;

use App\Entity\ProductTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductTypes[]    findAll()
 * @method ProductTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductTypes::class);
    }

    // /**
    //  * @return ProductTypes[] Returns an array of ProductTypes objects
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
    public function findOneBySomeField($value): ?ProductTypes
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
