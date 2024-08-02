<?php

namespace App\Controller\Admin;

use App\Entity\AccountEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Bundle\SecurityBundle\Security;

class AccountEntityCrudController extends AbstractCrudController {

  public function __construct(private Security $security, private EntityManagerInterface $entityManager)
  {
  }

  public static function getEntityFqcn(): string {
    return AccountEntity::class;
  }

  public function configureFilters(Filters $filters): Filters {
    return $filters
      ->add(EntityFilter::new('accountNumber'));
  }

  public function configureFields(string $pageName): iterable {
    yield IdField::new('id')->hideOnForm();
    yield textField::new('organisationNumber');
    yield TextField::new('accountNumber');
    yield TextField::new('location');
    yield TextField::new('name');
  }

  public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
  {
    $user = $this->getUser();

    // If the user has ROLE_SUPER_ADMIN, return all entities
    if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
      return $this->entityManager->createQueryBuilder('entity')
        ->select('entity')
        ->from(AccountEntity::class, 'entity');
    }

    // Otherwise, filter by the user's account entity
    return $this->entityManager->createQueryBuilder('entity')
      ->select('entity')
      ->from(AccountEntity::class, 'entity')
      ->where('entity.id = :accountEntity')
      ->setParameter('accountEntity', $user->getAccountEntity()->getId());
  }

}
