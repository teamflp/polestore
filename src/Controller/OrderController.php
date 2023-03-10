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
        $this -> em = $em;
    }

    #[Route('/commande', name: 'order')]
    public function index(Cart $cart): Response
    {
        // Si l'utilisateur n'a pas d'adresse, on le redirige vers la page d'ajout d'adresse
        if (!$this -> getUser() -> getAddresses() -> getValues()) {
            return $this -> redirectToRoute('account_address_add');
        }

        $form = $this -> createForm(OrderType::class, null, [ // null = pas de données par défaut
            'user' => $this -> getUser(),  // On passe l'utilisateur connecté à notre formulaire pour qu'il puisse choisir son adresse de livraison
        ]);

        if ($form -> isSubmitted() && $form -> isValid()) {
            //dd($form->getData());
            $form -> getData();
        }

        return $this -> render('order/index.html.twig', [
            'cart' => $cart -> getFull(),
            'form' => $form -> createView(),
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'order_recap')]
    public function add(Cart $cart, Request $request): Response
    {

        $carriers = null;
        $delivery_content = null;
        $form = $this -> createForm(OrderType::class, null, [
            'user' => $this -> getUser(),
        ]);

        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {
            $date = new \DateTime();
            $delivery = $form -> get('addresses') -> getData();
            $delivery_content = $delivery -> getFirstname() . ' ' . $delivery -> getLastname();
            $delivery_content .= '<br>' . $delivery -> getPhone();

            if ($delivery -> getCompany()) {
                $delivery_content .= '<br>' . $delivery -> getCompany();
            }

            $delivery_content .= '<br>' . $delivery -> getAddress();
            $delivery_content .= '<br>' . $delivery -> getPostal() . ' ' . $delivery -> getCity();
            $delivery_content .= '<br>' . $delivery -> getCountry() . '<br>';

            $order = new Order();
            $ref = $date->format('dmY').'-'.uniqid();
            $order->setRef($ref);
            $order -> setUser($this -> getUser());
            $carriers = $form -> get('carriers') -> getData();
            $order -> setCreatedAt($date);
            $order -> setCarrierName($carriers -> getName());
            $order -> setCarrierPrice($carriers -> getPrice());
            $order -> setDelivery($delivery_content);
            $order -> setState(0);

            // 0 = non valide, 1 = valide


            $this -> em -> persist($order);

            foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->em->persist($orderDetails);
            }

            //dd($order);

            $this->em->flush();

            return $this -> render('order/add.html.twig', parameters: [
                'cart' => $cart -> getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'ref' => $order->getRef(),
            ]);
        }
        return $this->redirectToRoute('cart');
    }
}
