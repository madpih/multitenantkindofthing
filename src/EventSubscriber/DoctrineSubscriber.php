<?php
namespace App\EventSubscriber;

use App\Entity\Admin;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

class DoctrineSubscriber implements EventSubscriber
{
  private $security;
  private $entityManager;

  public function __construct(Security $security, EntityManagerInterface $entityManager)
  {
    $this->security = $security;
    $this->entityManager = $entityManager;
  }

  public function getSubscribedEvents()
  {
    return [
      Events::postLoad,
      Events::onFlush,
    ];
  }

  public function postLoad(LifecycleEventArgs $args)
  {
    $this->applyFilter();
  }

  public function onFlush(LifecycleEventArgs $args)
  {
    $this->applyFilter();
  }

  private function applyFilter()
  {
    $user = $this->security->getUser();
    if ($user instanceof Admin) {
      $filter = $this->entityManager->getFilters()->enable('account_entity_filter');
      $filter->setParameter('account_entity_id', $user->getAccountEntity()->getId());
    }
  }
}
