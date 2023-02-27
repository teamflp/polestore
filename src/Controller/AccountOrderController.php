<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{
    #[Route('/account/commandes', name: 'account_order')]
    public function index(): Response
    {
        // On affiche toutes les commandes de l'utilisateur connecté

        return $this->render('account/order.html.twig');
    }
}
