<?php

namespace App\Controller;

use App\Classe\Cart;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session', name: 'stripe_create_session')]
    public function createSession(Cart $cart): JsonResponse
    {
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        $productsForStripe = [];

        foreach ($cart->getFull() as $product) {
            $productsForStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'images' => [$YOUR_DOMAIN . "/uploads/" . $product['product']->getIllustration()],
                    ],
                ],
                'quantity' => $product['quantity'],
                // On ajoute le prix du transporteur
            ];
        }

        Stripe::setApiKey('sk_test_51MctScAfpYZJnEmGpSaAbZEGqoeUQsjIh0mo25uFmXNWC0b0AUiZlDQkAGZHpJknmFDf5jyiFye9l7YFmDxzu4O4006S2kXHd4');

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $productsForStripe,
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
            /*'automatic_tax' => [
                'enabled' => true,
            ],*/
        ]);

        return new JsonResponse(['id' => $checkoutSession->id], 200, []);

    }
}
