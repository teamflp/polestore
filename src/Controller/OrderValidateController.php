<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderValidateController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this -> em = $em;
    }
    #[NoReturn] #[Route('/commande/success/{stripeSessionId}', name: 'order_validate')]
    public function index($stripeSessionId): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if ($order->getUser() != $this->getUser()) { // Si la commande n'existe pas ou si l'utilisateur n'est pas le bon
            return $this->redirectToRoute('home'); // On redirige vers la page d'accueil ou 404 si la commande n'existe pas ou
        }

        // 1- On met à jour le statut ispaid à 1 de la commande
        if (!$order->getIsPaid()) { // Si la commande n'est pas payée
            $order->setIsPaid(1); // 1 = payée sur Stripe, sinon 0 = non payée
            $this->em->flush();
            $this->addFlash('success', 'Votre commande a bien été validée');

            // Après avoir validé la commande, on vide le panier
           // $this->get('session')->remove('cart');

            // 2- On envoie un mail à l'utilisateur pour lui confirmer sa commande

        } else {
            $this->addFlash('warning', 'Cette commande a déjà été validée');
        }

        return $this->render('order_validate/index.html.twig', [
            'order' => $order,
        ]);
    }
}
