<?php

namespace App\Repository;

use App\Entity\AccountEntity;
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
        public function findActiveTodosByConference(Conference $conference, AccountEntity $accountEntity): array
        {
            return $this->createQueryBuilder('t')
                ->andWhere('t.conference = :conference')
                ->andWhere('t.isCompleted = :isCompleted')
                ->setParameter('conference', $conference)
                ->setParameter('isCompleted', 0)
                ->getQuery()
                ->getResult()
              ;
        }
          public function findByAccountEntity(AccountEntity $accountEntity)
          {
            return $this->createQueryBuilder('c')
              ->andWhere('c.accountEntity= :accountEntity')
              ->setParameter('accountEntity', $accountEntity)
              ->getQuery()
              ->getResult();
          }
}
