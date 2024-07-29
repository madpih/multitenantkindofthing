<?php

namespace App\Security\Voter;

use App\Entity\AccountEntity;
use App\Entity\Admin;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OrganisationVoter extends Voter
{

  public function __construct(
    private Security $security,
  ) {
  }
  const MANAGE = 'manage';
   protected function supports(string $attribute, $subject): bool {
    return in_array($attribute, [self::MANAGE]) && $subject instanceof AccountEntity;
  }

  protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool {
    $user = $token->getUser();

    // if the user is anonymous, do not grant access
    if (!$user instanceof UserInterface) {
      return false;
    }

    if (!$user instanceof Admin) {
      return false;
    }

    if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
      return true;
    }

    $accountEntity = $subject;
    switch ($attribute) {
      case self::MANAGE:
        return $this->canManage($accountEntity,$user);
    }

    return false;
  }

  private function canManage(AccountEntity $accountEntity, Admin $user) {
     $allowedRoles = ['ROLE_ADMIN','ROLE_USER'];
     $userRoles = $user->getRoles();
    // Check if the user has any of the allowed roles and if the user belongs to the given account entity
     return $user->getAccountEntity() === $accountEntity && !empty(array_intersect($allowedRoles, $userRoles));
  }

}