<?php

namespace App\Controller;

use App\Entity\AccountEntity;
use App\Entity\Admin;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new Admin();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $organisationNumber = $form->get('organisationNumber')->getData();
            $location = $form->get('location')->getData();

            if ($organisationNumber) {
              $accountEntity = $entityManager->getRepository(AccountEntity::class)
                ->findOneBy(['organisationNumber' => $organisationNumber]);

              if (!$accountEntity) {
                $accountEntity = new AccountEntity();
                $accountEntity->setOrganisationNumber($organisationNumber);
                $accountEntity->setLocation($location);
                $user->setRoles(['ROLE_ADMIN']);

                $entityManager->persist($accountEntity);
              } else {
                $user->setRoles(['ROLE_USER']);
              }

              $user->setAccountEntity($accountEntity);
            } else {
              // Handle the case where the organisation number is null or empty
              $this->addFlash('error', 'Organisation number is required.');
              return $this->redirectToRoute('app_register');
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
