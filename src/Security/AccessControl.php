<?php

namespace App\Security;

use App\Entity\AccountEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AccessControl
{
public function __construct(AuthorizationCheckerInterface $authorizationChecker)
{
}

  public function canAccessTenant(UserInterface $user): bool
  {
    if ($user->isSuperUser()) {
    return true;
    }

  return false;
  }
}
