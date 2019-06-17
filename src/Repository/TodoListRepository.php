<?php

namespace App\Repository;

use App\Entity\TodoList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

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
     * Select List elements and explicitly join Items to reduce DB calls on count elements
     *
     * @param array $ids
     *
     * @return mixed
     */
    public function findListJoinItems(array $ids = [])
    {
        $alias = 'list';

        $qb = $this->createQueryBuilder($alias);
        $qb->addSelect('item')
            ->leftJoin($alias.'.items', 'item');

        if (!empty($ids)) {
            if (count($ids) === 1)
                $qb->where($qb->expr()->eq($alias.'.id', '?1'));
            else
                $qb->where($qb->expr()->in($alias.'.id', '?1'));

            $qb->setParameter(1, $ids);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * @param TodoList $list
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(TodoList $list): void
    {
        $this->_em->remove($list);
        $this->_em->flush();
    }

    /**
     * @param TodoList $list
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(TodoList $list): void
    {
        $this->_em->persist($list);
        $this->_em->flush();
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
