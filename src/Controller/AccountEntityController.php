<?php

namespace App\Controller;

use App\Entity\AccountEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccountEntityController extends AbstractController
{
    #[Route('/account/{id}', name: 'app_account_entity')]
    public function show(AccountEntity $accountEntity): Response
    {
      $this->denyAccessUnlessGranted('manage', $accountEntity);

      return $this->render('account_entity/index.html.twig', [
        'account_entity' => $accountEntity,
      ]);
    }
}
