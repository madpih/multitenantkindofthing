<?php

namespace App\Repository;

use App\Entity\AccountEntity;
use App\Entity\Comment;
use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
  private const DAYS_BEFORE_REJECTED_REMOVAL = 7;
  public const COMMENTS_PER_PAGE = 2;
  public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

  public function countOldRejected(): int
  {
    return $this->getOldRejectedQueryBuilder()->select('COUNT(c.id)')->getQuery()->getSingleScalarResult();
  }

  public function deleteOldRejected(): int
  {
    return $this->getOldRejectedQueryBuilder()->delete()->getQuery()->execute();
  }

  private function getOldRejectedQueryBuilder(): QueryBuilder
  {
    return $this->createQueryBuilder('c')
      ->andWhere('c.state = :state_rejected or c.state = :state_spam')
      ->andWhere('c.createdAt < :date')
      ->setParameter('state_rejected', 'rejected')
      ->setParameter('state_spam', 'spam')
      ->setParameter('date', new \DateTimeImmutable(-self::DAYS_BEFORE_REJECTED_REMOVAL.' days'))
      ;
  }

    public function getCommentPaginator(Conference $conference, int $offset) : Paginator
    {

      $query = $this->createQueryBuilder('c')
        ->andWhere('c.conference = :conference')
        ->andWhere('c.state = :state')
        ->setParameter('conference', $conference)
        ->setParameter('state','published')
        ->orderBy('c.createdAt', 'DESC')
        ->setMaxResults(self::COMMENTS_PER_PAGE)
        ->setFirstResult($offset)
        ->getQuery();

      return new Paginator($query, true);
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