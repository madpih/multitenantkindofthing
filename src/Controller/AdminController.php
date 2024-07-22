<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Message\CommentMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Twig\Environment;

#[Route('/admin')]
class AdminController extends AbstractController
{

  public function __construct(
    private Environment $twig,
    private EntityManagerInterface $entityManager,
    private MessageBusInterface $bus,
  ) {
  }

//  #[Route('/admin/comment/review/{id}', name: 'review_comment')]
//  public function reviewComment(Request $request, Comment $comment, WorkflowInterface $commentStateMachine): Response
//   {
//     $accepted = !$request->query->get('rejected');
//
//     if ($commentStateMachine->can($comment, 'publish')) {
//       $transition = $accepted ? 'publish' : 'reject';
//     } elseif ($commentStateMachine->can($comment, 'publish_ham')) {
//       $transition = $accepted ? 'publish_ham' : 'reject_ham';
//     } else {
//       return new Response('Comment already reviewed or not in the right state.');
//     }
//
//     $commentStateMachine->apply($comment, $transition);
//     $this->entityManager->flush();
//
//     if ($accepted) {
//       $this->bus->dispatch(new CommentMessage($comment->getId()));
//     }
//      return new Response($this->twig->render('admin/review.html.twig', [
//        'transition' => $transition,
//        'comment' => $comment,
//      ]));
//   }

  #[Route('/comment/review/{id}', name: 'review_comment')]
  public function reviewComment(Request $request, Comment $comment, WorkflowInterface $commentStateMachine): Response
  {
    $rejectParam = $request->query->get('reject');
    $reject = filter_var($rejectParam, FILTER_VALIDATE_BOOLEAN);

    // Check for potential spam transitions first
    if ($commentStateMachine->can($comment, 'reject_spam')) {
      $transition = 'reject_spam';
    } elseif ($commentStateMachine->can($comment, 'might_be_spam')) {
      $transition = 'might_be_spam';
    } elseif ($reject) {
      // Handle rejection from 'ham' state
      if ($commentStateMachine->can($comment, 'reject_ham')) {
        $transition = 'reject_ham';
      } else {
        return new Response('Cannot reject the comment in its current state.');
      }
    } else {
      // Handle publishing from 'ham' state
      if ($commentStateMachine->can($comment, 'publish_ham')) {
        $transition = 'publish_ham';
      } else {
        return new Response('Cannot publish the comment in its current state.');
      }
    }

    $commentStateMachine->apply($comment, $transition);
    $this->entityManager->flush();

    if (!$reject) {
//      $this->bus->dispatch(new CommentMessage($comment->getId()));
      $reviewUrl = $this->generateUrl('review_comment', ['id' => $comment->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
      $this->bus->dispatch(new CommentMessage($comment->getId(), $reviewUrl));
    }

    return new Response($this->twig->render('admin/review.html.twig', [
      'transition' => $transition,
      'comment' => $comment,
    ]));
  }

  #[Route('/http-cache/{uri<.*>}', methods: ['PURGE'])]
  public function purgeHtpCache(KernelInterface $kernel, Request $request, string $uri, StoreInterface $store): Response
  {
    if ('prod' === $kernel->getEnvironment()) {
      return new Response('KO', 400);
    }
    $store->purge($request->getSchemeAndHttpHost() .'/'. $uri);

    return new Response('Done');
  }
}