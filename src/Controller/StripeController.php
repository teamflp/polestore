<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session/{ref}', name: 'stripe_create_session')]
    public function index(EntityManagerInterface $em, $ref): JsonResponse
    {
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        $productsForStripe = [];

        $order = $em->getRepository(Order::class)->findOneByRef(['ref' => $ref]);
       /* if (!$order instanceof Order) {
            throw new \RuntimeException('Order not found');
        }*/
        if (!$order) {
            new JsonResponse(['error' => 'order']);
        }

        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_object = $em->getRepository(Product::class)->findOneByName(['name' => $product->getProduct()]);
            //$product = $detail->getProduct();
            $productsForStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN . "/uploads/" . $product_object->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $productsForStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
            // On ajoute le prix du transporteur
        ];

        Stripe::setApiKey('sk_test_51MctScAfpYZJnEmGpSaAbZEGqoeUQsjIh0mo25uFmXNWC0b0AUiZlDQkAGZHpJknmFDf5jyiFye9l7YFmDxzu4O4006S2kXHd4');

        $checkoutSession = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => $productsForStripe,
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/failed/{CHECKOUT_SESSION_ID}',
            /*'automatic_tax' => [
                'enabled' => true,
            ],*/
        ]);

        $order->setStripeSessionId($checkoutSession->id); // On enregistre l'id de la session Stripe dans notre commande

        $em->flush(); // On enregistre en base de données

        return new JsonResponse(['id' => $checkoutSession->id], 200, []); // On retourne l'id de la session Stripe au format JSON

    }
}
