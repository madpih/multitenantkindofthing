<?php

namespace App\Command;

use App\Entity\AccountEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
  name: 'app:setup-default-account',
  description: 'Sets up a default AccountEntity'
)]
class SetupDefaultAccountEntityCommand extends Command
{
  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;

    parent::__construct();
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    // Fetch the default AccountEntity by a unique attribute
    $defaultAccountEntityName = 'Default Account';
    $accountEntity = $this->entityManager->getRepository(AccountEntity::class)
      ->findOneBy(['Name' => $defaultAccountEntityName]);

    if (!$accountEntity) {
      $io->success('Creating default AccountEntity.');

      // Create default AccountEntity
      $accountEntity = new AccountEntity();
      $accountEntity->setName($defaultAccountEntityName);
      $accountEntity->setOrganisationNumber(99999);
      $accountEntity->setLocation('EE');
      $accountEntity->setAccountNumber(99999);

      $this->entityManager->persist($accountEntity);
      $this->entityManager->flush();

      $io->success('Default AccountEntity created successfully.');
    } else {
      $io->success('Default AccountEntity already exists.');
    }
    return Command::SUCCESS;
  }
}
