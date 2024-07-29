<?php

namespace App\Controller\Admin;

use App\Entity\AccountEntity;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class AccountEntityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AccountEntity::class;
    }

  public function configureFilters(Filters $filters): Filters {
    return $filters
      ->add(EntityFilter::new('accountNumber'));
  }

  public function configureFields(string $pageName): iterable
  {
    yield IdField::new('id') ->hideOnForm();
    yield textField::new('organisationNumber');
    yield TextField::new('accountNumber');
    yield TextField::new('location');
    yield TextField::new('name');
  }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
