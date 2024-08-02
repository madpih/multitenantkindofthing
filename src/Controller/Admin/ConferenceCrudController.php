<?php
namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Conference;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Bundle\SecurityBundle\Security;


class ConferenceCrudController extends AbstractCrudController
{
       public function __construct(private Security $security, private EntityManagerInterface $entityManager)
    {
      }

    public static function getEntityFqcn(): string
    {
      return Conference::class;
    }

    public function createEntity(string $entityFqcn)
    {
    $conference = new Conference();

    $user = $this->security->getUser();

    if ($user instanceof Admin) {
      $accountEntity = $user->getAccountEntity();
      $conference->setAccountEntity($accountEntity);
    } else {
    throw new \LogicException('The current user is not an Admin or has no associated AccountEntity.');
    }

    return $conference;
    }

  public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
  {
    $user = $this->getUser();

    // If the user has ROLE_SUPER_ADMIN, return all entities
    if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
      return $this->entityManager->createQueryBuilder()
        ->select('entity')
        ->from(Conference::class, 'entity');
    }

    // Otherwise, filter by the user's account entity
    return $this->entityManager->createQueryBuilder()
      ->select('entity')
      ->from(Conference::class, 'entity')
      ->where('entity.accountEntity = :accountEntity')
      ->setParameter('accountEntity', $user->getAccountEntity());
  }
}
