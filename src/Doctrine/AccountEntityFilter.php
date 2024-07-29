<?php
namespace App\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class AccountEntityFilter extends SQLFilter
{
  public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string {
  // Check if the entity has the organisation field
  if (!$targetEntity->hasAssociation('accountEntity')) {
      return '';
  }
   return sprintf('%s.account_entity_id = %s', $targetTableAlias, $this->getParameter('account_entity_id'));
  }
}
