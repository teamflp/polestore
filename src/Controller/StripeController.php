<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session/{ref}', name: 'stripe_create_session')]
    public function index(EntityManagerInterface $em, Cart $cart, $ref): JsonResponse
    {
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        $productsForStripe = [];

        $order = $em->getRepository(Order::class)->findOneByRef(['ref' => $ref]);
        if (!$order instanceof Order) {
            throw new \RuntimeException('Order not found');
        }
       /* if (!$order) {
            new JsonResponse(['error' => 'order']);
        }*/

        foreach ($order->getOrderDetails() as $detail) {
            $product = $detail->getProduct();
            $productsForStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $detail->getPrice(),
                    'product_data' => [
                        'name' => $product->getName(),
                        'images' => [$YOUR_DOMAIN . "/uploads/" . $product->getIllustration()],
                    ],
                ],
                'quantity' => $detail->getQuantity(),
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
