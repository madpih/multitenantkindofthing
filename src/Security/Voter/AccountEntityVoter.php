<?php
namespace App\Security\Voter;

use App\Entity\AccountEntity;
use App\Entity\Admin;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccountEntityVoter extends Voter
{
private const MANAGE = 'manage';

public function __construct(
private Security $security,
) {
}

protected function supports(string $attribute, $subject): bool
{
return $attribute === self::MANAGE && $subject instanceof AccountEntity;
}

protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
{
$user = $token->getUser();

// if the user is anonymous, do not grant access
if (!$user instanceof Admin) {
return false;
}

// Check for superadmin role
if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
return true;
}

// Check if the user has permission to manage the AccountEntity
return $this->canManage($subject, $user);
}

private function canManage(AccountEntity $accountEntity, Admin $user): bool
{
$allowedRoles = ['ROLE_ADMIN', 'ROLE_USER'];
$userRoles = $user->getRoles();

// Check if the user has any of the allowed roles and if the user belongs to the given account entity
return $user->getAccountEntity() === $accountEntity && !empty(array_intersect($allowedRoles, $userRoles));
}
}
