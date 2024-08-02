<?php

namespace App\Command;

use App\Entity\AccountEntity;
use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
  name: 'app:create-super-user',
  description: 'Creates a super-user account'
)]
class CreateSuperUserCommand extends Command
{
  private EntityManagerInterface $entityManager;
  private UserPasswordHasherInterface $passwordHasher;

  public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
  {
    $this->entityManager = $entityManager;
    $this->passwordHasher = $passwordHasher;

    parent::__construct();
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    $username = 'superuser';
    $password = 'superpassword';

    $defaultAccountEntityName = 'Default Account';
    $accountEntity = $this->entityManager->getRepository(AccountEntity::class)->findOneBy(['Name' => $defaultAccountEntityName]);

    if (!$accountEntity) {
      $io->error('Default AccountEntity not found.');
      return Command::FAILURE;
    }

    // Check if the super-user already exists
    $user = $this->entityManager->getRepository(Admin::class)->findOneBy(['username' => $username]);

    if (!$user) {
      $user = new Admin();
      $user->setUsername($username);
      $user->setRoles(['ROLE_SUPER_ADMIN']);
      $user->setPassword($this->passwordHasher->hashPassword($user, $password));
      $user->setAccountEntity($accountEntity); // Attach the default AccountEntity

      $this->entityManager->persist($user);
      $this->entityManager->flush();

      $io->success('Super-user created successfully.');
    } else {
      $io->error('Super-user already exists.');
    }

    return Command::SUCCESS;
  }
}
