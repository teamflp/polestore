<?php

namespace App\Controller;

use App\Classe\RegisterEmailService;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private EntityManagerInterface $em;
    private RegisterEmailService $emailService;

    const SUCCESS_MESSAGE = 'Votre compte a bien été créé. Vous pouvez dès à présent vous connecter.';
    const WARNING_MESSAGE = 'L\'email renseignée existe déjà. Vous pouvez vous connecter.';

    public function __construct(EntityManagerInterface $em, RegisterEmailService $emailService)
    {
        $this->em = $em;
        $this->emailService = $emailService;
    }

    #[Route('/register', name: 'inscription')]
    public function index(Request $request, UserPasswordHasherInterface $hasher, LoggerInterface $logger): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            // On vérifie si l'utilisateur n'existe pas déjà dans la base de données
            $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

            if (!$existingUser) {
                // Hasher le mot de passe
                $hashedPassword = $hasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);

                // Enregistrer l'utilisateur dans la base de données
                $this->em->persist($user);
                $this->em->flush();

                // Envoyer un email de confirmation à l'utilisateur
                $this->emailService->sendRegistrationConfirmationEmail($user);

                $this->addFlash('success', self::SUCCESS_MESSAGE);

                // Une fois le compte créé on vide le formulaire
                return $this->redirectToRoute('inscription');
            } else {
                $this->addFlash('warning', self::WARNING_MESSAGE);
            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
