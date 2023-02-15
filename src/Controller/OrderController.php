<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('/commande', name: 'order')]
    public function index(Cart $cart, Request $request): Response
    {
        // Si l'utilisateur n'a pas d'adresse, on le redirige vers la page d'ajout d'adresse
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('account_address_add');
        }

        $form = $this->createForm(OrderType::class, null, [ // null = pas de données par défaut
            'user' => $this->getUser(),  // On passe l'utilisateur connecté à notre formulaire pour qu'il puisse choisir son adresse de livraison
        ]);

        if($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());
            $form->getData();
        }

        return $this->render('order/index.html.twig', [
            'cart' => $cart->getFull(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'order_recap', methods: ['POST'])]
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this -> createForm(OrderType::class, null, [ // null = pas de données par défaut
            'user' => $this -> getUser(),  // On passe l'utilisateur connecté à notre formulaire pour qu'il puisse choisir son adresse de livraison
        ]);

        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {
            $date = new \DateTime();
            $carriers = $form -> get('carriers') -> getData();
            $delivery = $form -> get('addresses') -> getData();
            //$delivery= $date->format('d/m/Y');
            $delivery_content = $delivery -> getFirstname() . ' ' . $delivery -> getLastname();
            $delivery_content .= '<br>' . $delivery -> getPhone();

            // Si la compagnie est renseignée
            if ($delivery -> getCompany()) {
                $delivery_content .= '<br>' . $delivery -> getCompany();
            }

            $delivery_content .= '<br>' . $delivery -> getAddress();
            $delivery_content .= '<br>' . $delivery -> getPostal() . ' ' . $delivery -> getCity();
            $delivery_content .= '<br>' . $delivery -> getCountry() . '<br>';

            //dd($delivery_content);

            // Enregistrer la commande : Order
            $order = new Order();
            $order -> setUser($this -> getUser());
            $order -> setCreatedAt($date);
            $order -> setCarrierName($carriers -> getName());
            $order -> setCarrierPrice($carriers -> getPrice());
            $order -> setDelivery($delivery_content);
            $order -> setIsPaid(0); // 0 = non payé

            $this -> em -> persist($order);

            /*
             * Enregistrer mes produits : OrderDetails
             * Pour chaque produit du panier, on va créer une ligne OrderDetails
             */
            foreach ($cart -> getFull() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails -> setMyOrder($order);
                $orderDetails -> setProduct($product['product'] -> getName());
                $orderDetails -> setQuantity($product['quantity']);
                $orderDetails -> setPrice($product['product'] -> getPrice());
                $orderDetails -> setTotal($product['product'] -> getPrice() * $product['quantity']);
                //dd($orderDetails);
                $this -> em -> persist($orderDetails);
            }

            //$this->em->flush();
            return $this -> render('order/add.html.twig', [
                'cart' => $cart -> getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
            ]);

        }
        return $this->redirectToRoute('cart');
    }

}