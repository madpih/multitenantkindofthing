<?php

namespace App\EventListener;

use AllowDynamicProperties;
use App\Entity\AccountEntityAwareInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

#[AllowDynamicProperties] class AccountEntityListener
{
  public function __construct(Security $security, LoggerInterface $logger)
  {
    $this->security = $security;
    $this->logger = $logger;
  }

  public function prePersist(LifecycleEventArgs $event)
  {
    $entity = $event->getObject();

    if ($entity instanceof AccountEntityAwareInterface) {
      $user = $this->security->getUser();
      $this->logger->info('PrePersist event triggered for entity', ['entity' => get_class($entity)]);

      if ($user) {
      $accountEntity = $user->getAccountEntity();
      $entity->setAccountEntity($accountEntity);
      $this->logger->info('AccountEntity set in entity', [
        'entity' => get_class($entity),
        'accountEntity' => $accountEntity ? $accountEntity->getId() : null,
      ]);
    } else {
        $this->logger->warning('No user found in security context');
      }
    }
  }
}
