<?php

namespace App\Controller\Admin;

use App\Entity\AccountEntity;
use App\Entity\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Conference;
use App\Entity\Comment;
use App\Entity\TodoList;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DashboardController extends AbstractDashboardController {

  #[Route('/admin', name: 'admin')]
  public function index():Response {

    $user = $this->getUser();
        if (!$user instanceof Admin) {
            throw $this->createAccessDeniedException();
        }

    $accountEntity = $user->getAccountEntity();
    $this->denyAccessUnlessGranted('manage', $accountEntity);

    $routeBuilder = $this->container->get(AdminUrlGenerator::class);
    $url = $routeBuilder->setController(ConferenceCrudController::class)
      ->generateUrl();

    return $this->redirect($url);
  }

  public function configureDashboard(): Dashboard {
    return Dashboard::new()
      ->setTitle('Guestbook');
  }

  public function configureMenuItems(): iterable {
    yield MenuItem::linkToRoute('Back to the website', 'fa fa-home', 'homepage');
    yield MenuItem::linkToCrud('Conferences', 'fas fa-map-market-alt', Conference::class);
    yield MenuItem::linkToCrud('Comments', 'fas fa-comments', Comment::class);
    yield MenuItem::linkToCrud('ToDo List', 'fas fa-list-ul', TodoList::class);
    yield MenuItem::linkToCrud('Users', 'fa fa-user', Admin::class);
    yield MenuItem::linkToCrud('AccountEntity', 'fa fa-user', AccountEntity::class);
  }

}
