<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/compte/adresses', name: 'account_address')]
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }

    #[Route('/compte/ajouter-une-adresse', name: 'account_address_add')]
    public function add(Cart $cart, Request $request): Response
    {
        $address = new Address();

        /**
         * Cette ligne de code crée un objet "formulaire" en utilisant la classe "AddressType" et l'objet "address".
         * "AddressType" est probablement une classe de type de formulaire qui définit les champs et les règles pour un formulaire d'adresse.
         * L'objet "address" sera utilisé pour préremplir les valeurs des champs du formulaire si l'adresse existe déjà,
         * sinon il sera utilisé pour stocker les données entrées dans le formulaire.
         * La méthode "createForm" est une méthode de l'objet courant qui crée un objet formulaire en utilisant
         * la classe de type de formulaire spécifiée et l'objet de données associé.
         */
        $form = $this->createForm(AddressType::class, $address);

        /**
         * Cette ligne de code traite une requête HTTP en utilisant l'objet "formulaire".
         * La méthode "handleRequest" prend en entrée un objet "request" qui représente une requête HTTP et met à jour l'objet "formulaire"
         * en utilisant les données soumises dans la requête.
         * Cela signifie que les données soumises dans la requête sont extraites et utilisées pour mettre à jour les valeurs associées aux champs du formulaire.
         * Si les données soumises sont valides selon les règles définies dans la classe "AddressType", alors les données seront correctement mises à jour dans l'objet "address".
         */
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $this->em->persist($address);
            $this->em->flush();

            /**
             * Si l'utilisateur n'avait pas d'adresse et qu'il provenait du tunnel de paiement et qu'il est redirigé vers la page d'ajout d'adresse
             * pour ajouter une adresse.
             * Une fois l'adresse ajoutée, on le redirige vers la page d'où il provenait (du tunnel de paiement).
             */
            if($cart->get()) {
                return $this->redirectToRoute('order');
            } else {
                $this->addFlash('success', 'Votre adresse a été ajoutée avec succès');
            }

        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Modification de l'adresse d'un utilisateur
    #[Route('/compte/modifier-une-adresse/{id}', name: 'account_address_edit')]
    public function modifier(Request $request, $id): Response
    {
        $address = $this->em->getRepository(Address::class)->findOneBy(['id' => $id]);

        // Si l'adresse n'existe pas ou si l'utilisateur n'est pas le propriétaire de l'adresse
        if (!$address || $address->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('account_address');
        }
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Votre adresse a été modifié avec succès');
            // On redirige l'utilisateur vers la page des adresses après 5 secondes
            //return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Suppression d'une adresse
    #[Route('/compte/supprimer-une-adresse/{id}', name: 'account_address_delete')]
    public function delete($id): Response
    {
        $address = $this->em->getRepository(Address::class)->findOneBy(['id' => $id]);

        // Si l'adresse existe et que l'utilisateur est le propriétaire de l'adresse
        if ($address && $address->getUser() === $this->getUser()) {
            $this->em->remove($address);
            $this->em->flush();
            $this->addFlash('success', 'Votre adresse a été supprimée avec succès');
        }

        return $this->redirectToRoute('account_address');
    }
}
