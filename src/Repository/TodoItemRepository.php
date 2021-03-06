<?php

namespace App\Repository;

use App\Entity\TodoItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TodoItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method TodoItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method TodoItem[]    findAll()
 * @method TodoItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoItemRepository extends ServiceEntityRepository
{
    /**
     * TodoItemRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TodoItem::class);
    }

    /**
     * @param string $query
     *
     * @return mixed
     */
    public function searchItems(string $query)
    {
        $alias = 'item';

        $qb = $this->createQueryBuilder($alias);

        return $qb->addSelect('list')
            ->leftJoin($alias.'.list', 'list')
            ->where($qb->expr()->like($alias.'.description', $qb->expr()->literal('%'.$query.'%')))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param TodoItem $item
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(TodoItem $item): void
    {
        $this->_em->remove($item);
        $this->_em->flush();
    }

    /**
     * @param TodoItem $item
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(TodoItem $item): void
    {
        $this->_em->persist($item);
        $this->_em->flush();
    }


    // /**
    //  * @return TodoItem[] Returns an array of TodoItem objects
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
    public function findOneBySomeField($value): ?TodoItem
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
