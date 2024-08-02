<?php

namespace App\Entity;

interface AccountEntityAwareInterface
{
  public function setAccountEntity(AccountEntity $accountEntity);
}