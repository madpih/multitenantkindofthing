<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\TodoList;
use App\Form\CommentType;
use App\Form\TaskType;
use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use App\Repository\TodoListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class ConferenceController extends AbstractController

{
    public function __construct(
      private EntityManagerInterface $entityManager,
      private MessageBusInterface $bus,
    ) {

    }

    #[Route('/')]
    public function indexNoLocale(): Response
    {
     return $this->redirectToRoute('homepage', ['_locale' => 'en']);
    }

    #[Route('/{_locale<%app.supported_locales%>}/', name: 'homepage')]
    public function index(ConferenceRepository $conferenceRepository): Response
    {
      return $this->render('conference/index.html.twig', [
        'conferences' => $conferenceRepository->findAll(),
      ])->setSharedMaxAge(0);

    }

    #[Route('/{_locale<%app.supported_locales%>}/conference_header', name: 'conference_header')]
    public function conferenceHeader(ConferenceRepository $conferenceRepository): Response
    {
      return $this->render('conference/header.html.twig', [
        'conferences' => $conferenceRepository->findAll(),
      ]);
    }

    #[Route('/{_locale<%app.supported_locales%>}/conference/{slug}', name: 'conference')]
    public function show(
      Request $request,
      string $slug,
      TodoListRepository $todoListRepository,
      ConferenceRepository $conferenceRepository,
      CommentRepository $commentRepository,
      NotifierInterface $notifier,
      #[Autowire('%photo_dir%')] string $photoDir,
    ): Response {
      $conference = $conferenceRepository->findOneBySlug($slug);
      $todos =$todoListRepository->findActiveTodosByConference($conference->getId());

      $todo = new TodoList();
      $add_todo_form = $this->createForm(TaskType::class, $todo);
      $add_todo_form->handleRequest($request);
      if($add_todo_form->isSubmitted() && $add_todo_form->isValid()) {
        $todo->setConference($conference);
        $this->entityManager->persist($todo);
        $this->entityManager->flush();

        return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);

      }

      $comment = new Comment();
      $form = $this->createForm(CommentType::class, $comment);
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $comment->setConference($conference);
        if ($photo = $form['photo']->getData()) {
          $filename = bin2hex(random_bytes(6)).'.'.$photo->guessExtension();
          $photo->move($photoDir, $filename);
          $comment->setPhotoFilename($filename);
        }


        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        $context = [
                     'user_ip' => $request->getClientIp(),
                     'user_agent' => $request->headers->get('user-agent'),
                     'referrer' => $request->headers->get('referer'),
                     'permalink' => $request->getUri(),
        ];


        $reviewUrl = $this->generateUrl('review_comment', ['id' => $comment->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->bus->dispatch(new CommentMessage($comment->getId(), $reviewUrl, $context));
        $notifier->send(new Notification('Thank you for the feedback! Your comment will be posted after moderation.', ['browser']));

        return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);
      }
      if ($form->isSubmitted()) {
          $notifier->send(new Notification('Can you check your submission? There are some problems with it.', ['browser']));
      }

      $offset = max(0, $request->query->getInt('offset', 0));
      $paginator = $commentRepository->getCommentPaginator($conference, $offset);


      return $this->render('conference/show.html.twig', [
        'conference' => $conference,
        'comments' => $paginator,
        'todos' =>$todos,
        'previous' => $offset - CommentRepository::COMMENTS_PER_PAGE,
        'next' => min(count($paginator),$offset + CommentRepository::COMMENTS_PER_PAGE),
        'comment_form'=>$form,
        'add_todo_form' =>$add_todo_form,
      ]);
    }

}
