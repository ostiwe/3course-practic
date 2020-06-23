<?php

namespace App\Repository;

use App\Entity\AutoModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AutoModel|null find($id, $lockMode = null, $lockVersion = null)
 * @method AutoModel|null findOneBy(array $criteria, array $orderBy = null)
 * @method AutoModel[]    findAll()
 * @method AutoModel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutoModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AutoModel::class);
    }

    // /**
    //  * @return AutoModel[] Returns an array of AutoModel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AutoModel
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
