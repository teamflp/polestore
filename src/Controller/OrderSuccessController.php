<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private EntityManagerInterface $em;
    private Cart $cart;
    private Mail $mail;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, Cart $cart, Mail $mail, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->cart = $cart;
        $this->mail = $mail;
        $this->logger = $logger;
    }

    #[Route('/commande/success/{stripeSessionId}', name: 'order_success')]
    public function index($stripeSessionId): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() !== $this->getUser()) { // Si la commande n'existe pas ou si l'utilisateur n'est pas le bon
            return $this->redirectToRoute('home');
        }

        if ($order->getState()) {
            $this->addFlash('warning', 'Cette commande a déjà été validée');
            return $this->render('order_success/index.html.twig', ['order' => $order]);
        }

        $order->setState(1); // Commande validée
        $this->em->flush();
        $this->addFlash('success', 'Votre commande a bien été validée.');

        $content = "Bonjour, ". $order->getUser()->getFirstName(). ' '. $order->getUser()->getLastName() . "\n Merci pour votre commande sur notre boutique.\n\n";
        $this->mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(). ' '. $order->getUser()->getLastName(), 'Votre commande a bien été validée', $content);
        $this->logger->info('Order success email sent', ['to_email' => $order->getUser()->getEmail()]);

        $this->cart->remove(); // Utilisation de la méthode "remove()" de l'objet Cart pour vider le panier

        return $this->render('order_success/index.html.twig', ['order' => $order]);
    }
}
