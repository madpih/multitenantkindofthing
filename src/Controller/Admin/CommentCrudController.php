<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Conference;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class CommentCrudController extends AbstractCrudController
{

  public function __construct(private readonly EntityManagerInterface $entityManager) {}

  public static function getEntityFqcn(): string
  {
    return Comment::class;
  }

  public function configureCrud(Crud $crud): Crud
  {
    return $crud
      ->setEntityLabelInSingular('Conference Comment')
      ->setEntityLabelInPlural('Conference Comments')
      ->setSearchFields(['author', 'text', 'email'])
      ->setDefaultSort(['createdAt' => 'DESC'])
      ->setPaginatorPageSize(10)
      ->setPaginatorRangeSize(4)

      ;
  }

  public function configureFilters(Filters $filters): Filters
  {
    return $filters
      ->add(EntityFilter::new('conference'));
  }

  public function configureFields(string $pageName): iterable
  {

    yield AssociationField::new('conference');
    yield TextField::new('author');
    yield TextField::new('email');
    yield TextAreaField::new('text');
    yield ImageField::new('photoFilename')
      ->setBasePath('/uploads/photos')
      ->setLabel('Photo')
      ->onlyOnIndex();
    yield TextField::new('state');
    yield DateTimeField::new('createdAt') ->onlyOnIndex();;

    $createdAt = DateTimeField::new('createdAt')->setFormTypeOptions([
      'years' => range(date('Y'), date('Y') + 5),
      'widget' => 'single_text',
    ]);
    if (Crud::PAGE_EDIT === $pageName) {
        yield $createdAt->setFormTypeOption('disabled', true);

    }

  }

}
