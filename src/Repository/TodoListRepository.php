<?php

namespace App\Repository;

use App\Entity\TodoList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query;

/**
 * @method TodoList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TodoList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TodoList[]    findAll()
 * @method TodoList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoListRepository extends ServiceEntityRepository
{
    /**
     * TodoListRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TodoList::class);
    }

    /**
     * Selects all List elements and counts associative Items
     *
     * @return mixed
     */
    public function findAllCountItems()
    {
        $alias = 'list';

        // select only "real" entity fields WITHOUT associative collection
        $fieldNames = array_map(function ($v) use ($alias) {
            return ($alias . '.' . $v);
        }, $this->getClassMetadata()->getFieldNames());

        return $this->createQueryBuilder($alias)
            ->select($fieldNames)
            ->addSelect('COUNT(i.id) as items_count')
            ->leftJoin($alias . '.items', 'i')
            ->groupBy($alias . '.id')
            ->getQuery()
            ->getArrayResult()
            ;

    }

    // /**
    //  * @return TodoList[] Returns an array of TodoList objects
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
    public function findOneBySomeField($value): ?TodoList
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
