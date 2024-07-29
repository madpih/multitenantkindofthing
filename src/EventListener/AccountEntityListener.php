<?php

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

class AccountEntityListener
{
  private $security;

  public function __construct(Security $security)
  {
    $this->security = $security;
  }

  public function prePersist(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();
    $user = $this->security->getUser();

    if (method_exists($entity, 'setAccountEntityId') && method_exists($user, 'getAccountEntityId')) {
      $entity->setAccountEntityId($user->getAccountEntityId());
    }
  }
}
