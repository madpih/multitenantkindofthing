<?php

namespace App\Repository;

use App\Entity\AccountEntity;
use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conference>
 */
class ConferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conference::class);
    }

    public function findAll():array
    {
      return $this->findby([], ['year' => 'ASC', 'city' => 'ASC']);
    }

  public function findOneBySlug(string $slug,AccountEntity $accountEntity): ?Conference
  {

    return $this->createQueryBuilder('c')
      ->andWhere('c.slug = :slug')
      ->andWhere('c.accountEntity = :accountEntity')
      ->setParameter('slug', $slug)
      ->setParameter('accountEntity', $accountEntity)
      ->getQuery()
      ->getOneOrNullResult();
  }

//  public function findByAccountEntity(AccountEntity $accountEntity): array
//  {
//    return $this->createQueryBuilder('c')
//      ->andWhere('c.accountEntity = :accountEntity')
//      ->setParameter('accountEntity', $accountEntity)
//      ->orderBy('c.year', 'ASC') // Adjust ordering as needed
//      ->getQuery()
//      ->getResult();
//  }

}
