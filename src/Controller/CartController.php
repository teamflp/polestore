<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $cart;
    private SessionInterface $session;
    private $em;

    /**
     * Ajouter le service SessionInterface dans le fichier services.yaml
     * configurer "Symfony\Component\HttpFoundation\Session\SessionInterface"
     * dans les services pour permettre son injection de dépendance automatique :
     *
     * Services.yaml
     *    Symfony\Component\HttpFoundation\Session\SessionInterface:
     *    class: Symfony\Component\HttpFoundation\Session\Session
     *    public: true
     */
    public function __construct(EntityManagerInterface $em, Cart $cart, SessionInterface $session)
    {
        $this->cart = $cart;
        $this->session = $session;
        $this->em = $em;
    }
    #[Route('/mon-panier', name: 'cart')]
    public function index(Cart $cart): Response
    {

        // On affiche les meilleurs produits
        $products = $this->em->getRepository(Product::class)->findOneBy(['isBest' => 1]);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getFull(),
            'products' => $products,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'add_to_cart')]
    public function add(Cart $cart, Request $request, $id): Response
    {
        $cart->add($id);




        return $this->redirectToRoute('products');
        // return $this->redirectToRoute('cart');
        //return $this->render('cart/index.html.twig');
    }

    #[Route('/cart/remove', name: 'remove_my_cart')]
    public function remove(Cart $cart): Response
    {
        $cart->remove();

        // On redirigera l'utilisateur vers la route nommée "products".
        return $this->redirectToRoute('products');
        //return $this->render('cart/index.html.twig');
    }

    #[Route('/cart/delete{id}', name: 'delete_to_cart')]
    public function delete(Cart $cart, $id): Response
    {
        $cart->delete($id);

        // On redirigera l'utilisateur vers la route nommée "cart".
        return $this->redirectToRoute('cart');
        //return $this->render('cart/index.html.twig');
    }

    #[Route('/cart/decrease{id}', name: 'decrease_to_cart')]
    public function decrease(Cart $cart, $id): Response
    {
        $cart->decrease($id);

        return $this->redirectToRoute('cart');
    }

    #[Route("/cart/favorite/{id}", name: 'cart_favorite')]

    public function favorites($id, Cart $cart): Response
    {
        $cart->favorites($id); // On ajoute le produit aux favoris

        // On ne fait aucune redirection vers une autre page, on reste sur la page show
        

        //return $this->redirectToRoute('cart');
    }
}
