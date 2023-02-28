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
        $search = new Search();

        $search->string = $request->get('q', '');
        $search->categories = $request->get('categories', []);
        $search->productName = $request->get('productName', '');
        $search->categoryName = $request->get('categoryName', '');
        $search = $request->query->get('search');
        return $this->render('home/index.html.twig', [
            'search' => $search,
        ]);
    }
}
