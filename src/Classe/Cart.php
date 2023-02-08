<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private SessionInterface $session;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->session = $session;
        $this->em = $em;
    }

    public function add($id): void
    {
        // On récupère le panier dans la session, sinon on initialise un tableau vide
        $cart = $this->session->get('cart', []);

        // Si le produit existe déjà dans le panier, on incrémente sa quantité
        if (!empty($cart[$id])) {
            $cart[$id]++; // $cart[$id] = $cart[$id] + 1; On incrémente la quantité
        } else {
            // Sinon, on l'ajoute au panier avec une quantité de 1
            $cart[$id] = 1;
        }
        $this->session->set('cart', $cart);
    }

    public function get()
    {
        return $this->session->get('cart');
    }
    public function remove()
    {
        return $this->session->remove('cart');
    }

    public function delete($id)
    {
        $cart = $this->session->get('cart', []); // On récupère le panier dans la session
        unset($cart[$id]); // On supprime le produit du panier
        return $this->session->set('cart', $cart); // On met à jour le panier dans la session
    }

    public function decrease($id)
    {
        $cart = $this->session->get('cart', []); // On récupère le panier dans la session

        if ($cart[$id] > 1) {
            $cart[$id]--; // On décrémente la quantité
        } else {
            unset($cart[$id]); // Sinon, on supprime le produit du panier
        }

        return $this->session->set('cart', $cart); // On met à jour le panier dans la session
    }

    public function getFull(): array
    {
        $cartComplete = [];
        if ($this->get()) { // Si la session cart existe
            foreach ($this->get() as $id => $quantity) { // Pour chaque produit dans la session cart
                $product_object = $this->em->getRepository(Product::class)->findOneById($id);
                if (!$product_object) {
                    $this->delete($id);
                    continue;
                }
                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }
        }
        return $cartComplete;
    }

    public function favorites($id) {
        // Récupère les produits favoris dans la session, sinon initialise un tableau vide
        $favorites = $this->session->get('favorites', []);

        // Ajoute le produit en favori
        $favorites[$id] = true;

        // Enregistre les produits favoris dans la session
        $this->session->set('favorites', $favorites);
    }
}