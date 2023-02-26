<?php

namespace App\Controller;

use App\Classe\Search;
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
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $products = $this->em->getRepository(Product::class)->findWithSearch($search);
        } else {
            $products = $this->em->getRepository(Product::class)->findAll();
            $this->redirectToRoute('products');
        }

        //$search = new Search();
        $search->string = $request->get('q', '');
        $search->categories = $request->get('categories', []);
        $search->productName = $request->get('productName', '');
        $search->categoryName = $request->get('categoryName', '');

        $products = $this->em->getRepository(Product::class)->findWithSearch($search);
        $search = $request->query->get('search');

        //dd($products);
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'search' => $search,
            'form' => $form->createView()
        ]);
    }

    // Afficher un produit
    #[Route('/produit/{slug}', name: 'product')]
    public function show($slug): Response
    {
        $product = $this->em->getRepository(Product::class)->findOneBySlug($slug);

        if (!$product) {
            return $this->redirectToRoute('product');
        }
        //dd($product);
        return $this->render('product/show.html.twig', compact('product'));
    }
}