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
     * @param null $id
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findListJoinItems($id = null)
    {
        $alias = 'list';

        $qb = $this->createQueryBuilder($alias);
        $qb->addSelect('item')
            ->leftJoin($alias.'.items', 'item');

        if (!empty($id)) {
            $qb->where($qb->expr()->eq($alias.'.id', '?1'));
            $qb->setParameter(1, $id);
        }

        $q = $qb->getQuery();

        if (!empty($id)) {
            return $q->getOneOrNullResult();
        } else {
            return $q->getResult();
        }
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
    public function save(TodoList $list): void
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
