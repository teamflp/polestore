<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class OrderController extends AbstractController
{
    #[Route('/commande', name: 'order')]
    public function index(Cart $cart, Request $request): Response
    {
        // Si l'utilisateur n'a pas d'adresse, on le redirige vers la page d'ajout d'adresse
        /*if (!$this->getUser()->getAddresses()->getValues()) { // Si l'utilisateur n'a pas d'adresse, on le redirige vers la page d'ajout d'adresse
            //$this->addFlash('warning', 'Vous devez ajouter une adresse avant de passer votre commande');
            return $this->redirectToRoute('account_address_add');
        }*/

        if ($this->getUser()->getAddresses()->isEmpty()) {
            return $this->redirectToRoute('account_address_add');
        }


        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $form->getData();
        }
        return $this->render('order/index.html.twig', [
            'cart' => $cart->getFull(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'order_recap')]
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
        }
            
            //Enregistrer mes produits
        return $this->render('order/add.html.twig', [
            'cart' => $cart->getFull(),
        ]);
    }
}
