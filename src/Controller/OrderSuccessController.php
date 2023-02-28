<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private EntityManagerInterface $em;
    private Cart $cart;

    public function __construct(EntityManagerInterface $em, Cart $cart)
    {
        $this->em = $em;
        $this->cart = $cart;
    }

    #[NoReturn]
    #[Route('/commande/success/{stripeSessionId}', name: 'order_success')]
    public function index($stripeSessionId): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() !== $this->getUser()) { // Si la commande n'existe pas ou si l'utilisateur n'est pas le bon
            return $this->redirectToRoute('home');
        }

        if ($order->getIsPaid()) {
            $this->addFlash('warning', 'Cette commande a déjà été validée');
            return $this->render('order_success/index.html.twig', ['order' => $order]);
        }

        $order->setIsPaid(true);
        $this->em->flush();
        $this->addFlash('success', 'Votre commande a bien été validée');

        $this->cart->remove(); // Utilisation de la méthode "remove()" de l'objet Cart pour vider le panier

        return $this->render('order_success/index.html.twig', ['order' => $order]);
    }
}