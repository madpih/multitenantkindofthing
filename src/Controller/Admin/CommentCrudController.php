<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\Conference;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Bundle\SecurityBundle\Security;

class CommentCrudController extends AbstractCrudController
{

  private Security $security;
  private EntityManagerInterface $entityManager;

  public function __construct(Security $security, EntityManagerInterface $entityManager)
  {
    $this->security = $security;
    $this->entityManager = $entityManager;
  }

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

  public function configureActions(Actions $actions): Actions
  {
    return $actions
      ->add(Crud::PAGE_EDIT, Action::INDEX)
      ->add(Crud::PAGE_INDEX, Action::DETAIL)
      ->add(Crud::PAGE_EDIT, Action::DETAIL)
      ;
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

  public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
  {
    $user = $this->getUser();

    // If the user has ROLE_SUPER_ADMIN, return all entities
    if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
      return $this->entityManager->createQueryBuilder()
        ->select('entity')
        ->from(Comment::class, 'entity');
    }

    // Otherwise, filter by the user's account entity
    return $this->entityManager->createQueryBuilder()
      ->select('entity')
      ->from(Comment::class, 'entity')
      ->where('entity.accountEntity = :accountEntity')
      ->setParameter('accountEntity', $user->getAccountEntity());
  }

}
