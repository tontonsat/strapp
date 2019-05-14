<?php

namespace App\Repository;

use App\Entity\Friendship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Friendship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friendship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friendship[]    findAll()
 * @method Friendship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    /**
    * @return Friendship[] Returns an array of Friendship objects
    */
    public function findAllMyFriendships()
    {
        return $this->createQueryBuilder('fs')
        ->where('fs.user = :user OR fs.friend = :user')
        ->setParameter('user', $this->getUser()->getId())
        ->getQuery()
        ->getResult();
    }

    /**
    * @return Friendship[] Returns an array of Friendship objects
    */
    public function findMyValidFriends($user)
    {
        return $this->createQueryBuilder('fs')
        ->where('fs.user = :user AND fs.status = 1')
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Friendship
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
