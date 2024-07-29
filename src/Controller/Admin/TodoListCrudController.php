<?php

namespace App\Controller\Admin;

use App\Entity\Conference;
use App\Entity\TodoList;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\VarDumper\VarDumper;

class TodoListCrudController extends AbstractCrudController
{
  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
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
}
