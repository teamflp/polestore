<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/account/commandes', name: 'account_order')]
    public function index(): Response
    {
        // On affiche toutes les commandes de l'utilisateur connecté
        $orders = $this->em->getRepository(Order::class)->findSuccessOrders($this->getUser());
        if (!$orders) {
            $this->addFlash('warning', 'Vous n\'avez pas encore passé de commande');
        }

        return $this->render('account/order.html.twig', [
            'orders' => $orders,
        ]);
    }
}
