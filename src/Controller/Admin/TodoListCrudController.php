<?php

namespace App\Controller\Admin;

use App\Entity\Conference;
use App\Entity\TodoList;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\VarDumper\VarDumper;

class TodoListCrudController extends AbstractCrudController
{
  private EntityManagerInterface $entityManager;
  private Security $security;

  public function __construct(Security $security, EntityManagerInterface $entityManager)
  {
    $this->security = $security;
    $this->entityManager = $entityManager;
  }

   public static function getEntityFqcn(): string
    {
        return TodoList::class;
    }

    public function configureFilters(Filters $filters): Filters {
      return $filters
        ->add(EntityFilter::new('conference'));
    }

    public function configureFields(string $pageName): iterable
    {
      yield AssociationField::new('conference');
      yield TextField::new('Task');
      yield TextAreaField::new('description');
      yield BooleanField::new('isCompleted');
    }

  public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
  {
    $user = $this->getUser();

    // If the user has ROLE_SUPER_ADMIN, return all entities
    if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
      return $this->entityManager->createQueryBuilder()
        ->select('entity')
        ->from(TodoList::class, 'entity');
    }

    // Otherwise, filter by the user's account entity
    return $this->entityManager->createQueryBuilder()
      ->select('entity')
      ->from(TodoList::class, 'entity')
      ->where('entity.accountEntity = :accountEntity')
      ->setParameter('accountEntity', $user->getAccountEntity());
  }
}
