<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud, KeyValueStore};
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

class AdminCrudController extends AbstractCrudController
{
    public function __construct(
    public UserPasswordHasherInterface $userPasswordHasher
  ) {}
    public static function getEntityFqcn(): string
    {
        return Admin::class;
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
        yield AssociationField::new('accountEntity');
        yield TextField::new('username');
        yield ChoiceField::new('roles')
          ->setChoices([
            'Admin' => 'ROLE_ADMIN',
            'User' => 'ROLE_USER',
          ])
          ->allowMultipleChoices();
        yield TextField::new('password')
          ->setFormType(RepeatedType::class)
          ->setFormTypeOptions([
            'type' => PasswordType::class,
            'first_options' => ['label' => 'Password'],
            'second_options' => ['label' => '(Repeat)'],
            'mapped' => false,
          ])
          ->setRequired($pageName === Crud::PAGE_NEW)
          ->onlyOnForms();
        yield BooleanField::new('isSuspended');
    }

  public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
  {
    $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);
    return $this->addPasswordEventListener($formBuilder);
  }

  public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
  {
    $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);
    return $this->addPasswordEventListener($formBuilder);
  }

  private function addPasswordEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
  {
    return $formBuilder->addEventListener(FormEvents::POST_SUBMIT, $this->hashPassword());
  }

  private function hashPassword() {
    return function($event) {
      $form = $event->getForm();
      if (!$form->isValid()) {
        return;
      }
      $password = $form->get('password')->getData();
      if ($password === null) {
        return;
      }

      $hash = $this->userPasswordHasher->hashPassword($this->getUser(), $password);
      $form->getData()->setPassword($hash);
    };
  }

  public function createEntity(string $entityFqcn)
  {
    $admin = new Admin();
    $admin->setRoles(['ROLE_USER']); // Set default roles

    return $admin;
  }

}
