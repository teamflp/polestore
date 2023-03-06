<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Category;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/nos-produits', name: 'products')]
    public function index(Request $request): Response
    {
        // Récupérer toutes les catégories depuis la base de données
        $categories = $this->em->getRepository(Category::class)->findAll();
        $priceRange = $this->em->getRepository(Product::class)->findPriceRange();

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $products = $this->em->getRepository(Product::class)->findWithSearch($search, (int)$priceRange['maxPrice']);
        } else {
            $products = $this->em->getRepository(Product::class)->findAll();
            $this->redirectToRoute('products');
        }

        //$search = new Search();
        $search->string = $request->get('q', '');
        $search->categories = $request->get('categories', []);
        $search->productName = $request->get('productName', '');
        $search->categoryName = $request->get('categoryName', '');
        $search->minPrice = intval($request->query->get('minPrice', $priceRange['minPrice']));
        $search->maxPrice = intval($request->query->get('maxPrice', $priceRange['maxPrice']));
        $products = $this->em->getRepository(Product::class)->findWithSearch($search, (int)$priceRange['minPrice']);



        $products = $this->em->getRepository(Product::class)->findWithSearch($search, (int)$priceRange['maxPrice'], (int)$priceRange['minPrice']);
        $search = $request->query->get('search');

        // Calculate price range
        $prices = array_map(function ($product) {
            return $product->getPrice();
        }, $products);

        if (count($prices) > 0) {
            $priceRange = ['min' => min($prices), 'max' => max($prices)];
        }

        //dd($products);
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'search' => $search,
            'form' => $form->createView(),
            'categories' => $categories, // Passer toutes les catégories au modèle
            'priceRange' => $priceRange ?? null, // Passer le tableau des prix au modèle
        ]);
    }



    #[Route('/produit/{slug}', name: 'product')]
    public function show($slug): Response
    {
        $product = $this->em->getRepository(Product::class)->findOneBySlug($slug);

        if (!$product) {
            return $this->redirectToRoute('product');
        }

        return $this->render('product/show.html.twig', compact('product'));
    }
}
