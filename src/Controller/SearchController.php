<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    public ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $term = $request->query->get('query');

        $products = $this->productRepository->searchProducts($term);

        return $this->render('search/index.html.twig', ['products' => $products]);

    }
}
