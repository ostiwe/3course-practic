<?php

namespace App\Repository;

use App\Entity\Auto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Auto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Auto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Auto[]    findAll()
 * @method Auto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Auto::class);
    }

	public function paginate(int $page = 1, int $limit = 5)
	{
		if ($page <= 0) $page = 1;
		if ($limit < 5) $limit = 5;

		$offset = ($page * $limit) - $limit;

		$sql = "SELECT au FROM App\Entity\Auto au ORDER BY au.createdAt DESC";
		$query = $this->_em->createQuery($sql)
			->setFirstResult($offset)
			->setMaxResults($limit);
		$res = [];
		$paginator = new Paginator($query, $fetchJoinCollection = true);

		/** @var Auto $item */
		foreach ($paginator as $item) {
			$res[] = $item->export();
		}

		return [
			'page' => $page,
			'offset' => $offset,
			'limit' => $limit,
			'items' => $res,
		];
	}

    // /**
    //  * @return Auto[] Returns an array of Auto objects
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
    public function findOneBySomeField($value): ?Auto
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
