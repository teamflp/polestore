<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    // UserPasswordHasherInterface est une interface qui permet de hasher le mot de passe
    // Request est une classe qui permet de récupérer les données du formulaire
    #[Route('/register', name: 'inscription')]
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User(); // instancie la classe User
        $form = $this->createForm(RegisterType::class, $user); // instancie la classe RegisterType

        $form->handleRequest($request); // récupère les données du formulaire

        if ($form->isSubmitted() && $form->isValid()) { // si le formulaire est soumis et valide
            $user = $form->getData(); // récupère les données du formulaire

            $password = $hasher->hashPassword($user, $user->getPassword());// hash le mot de passe
            $user->setPassword($password); // mot de passe hashé
            // dd($user);
            // dd($password);
            $this->em->persist($user); // prépare l'insertion
            $this->em->flush(); // exécute l'insertion

            // On affiche un message flash
            $this->addFlash('success', 'Votre compte a bien été créé');
        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
