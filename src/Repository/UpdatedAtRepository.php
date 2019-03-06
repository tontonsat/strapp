<?php

namespace App\Repository;

use App\Entity\UpdatedAt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UpdatedAt|null find($id, $lockMode = null, $lockVersion = null)
 * @method UpdatedAt|null findOneBy(array $criteria, array $orderBy = null)
 * @method UpdatedAt[]    findAll()
 * @method UpdatedAt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UpdatedAtRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UpdatedAt::class);
    }

    // /**
    //  * @return UpdatedAt[] Returns an array of UpdatedAt objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UpdatedAt
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
