<?php

namespace App\Repository;

use App\Entity\Conference;
use App\Entity\TodoList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TodoList>
 */
class TodoListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TodoList::class);
    }

    //    /**
    //     * @return TodoList[] Returns an array of TodoList objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

        public function findActiveTodosByConference($conferenceId): array
        {
            return $this->createQueryBuilder('t')
                ->andWhere('t.conference = :conferenceId')
                ->andWhere('t.isCompleted = :isCompleted')
                ->setParameter('conferenceId', $conferenceId)
                ->setParameter('isCompleted', 0)
                ->getQuery()
                ->getResult()
              ;
        }
}
