<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderFailedController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/commande/failed/{stripeSessionId}', name: 'order_failed')]
    public function index($stripeSessionId): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // On envoie à l'utilisateur pour lui indiquer que la commande a échoué
        $this->addFlash('warning', 'Votre paiement a échoué. Veuillez réessayer.');

        return $this->render('order_failed/index.html.twig', [
            'order' => $order,
        ]);
    }
}
