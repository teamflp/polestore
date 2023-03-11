<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Carousel;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        //$categories = $this->em->getRepository(Category::class)->findAll();
        // Affichage des meilleurs produits
        $products = $this->em->getRepository(Product::class)->findByIsBest(1);
        $carousels = $this->em->getRepository(Carousel::class)->findAll();

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'carousels' => $carousels,
        ]);
    }

    #[Route('/category/{id}', name: 'category_show')]
    public function showCategory(Request $request, Category $category): Response
    {
        $products = $this->em->getRepository(Product::class)->findBy(['category' => $category]);

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }
}
