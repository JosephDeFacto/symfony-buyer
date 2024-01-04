<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    public ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    #[Route('/', name: 'app_product')]
    public function index(): Response
    {
        $products = $this->productRepository->findAll();

        if (!$products) {
            throw $this->createNotFoundException('No products');
        }

        return $this->render('products/index.html.twig', ['products' => $products]);
    }

    #[Route('product/{id}', name: 'app_product_id')]
    public function show(Product $product): Response
    {
        return $this->render('products/show.html.twig' , ['product' => $product]);
    }
}
