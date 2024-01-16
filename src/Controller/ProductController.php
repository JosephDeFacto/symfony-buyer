<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    public ProductRepository $productRepository;
    public CategoryRepository $categoryRepository;


    public function __construct(ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;

    }
    #[Route('/', name: 'app_product')]
    public function index(): Response
    {
        $products = $this->productRepository->findAll();

        if (!$products) {
            throw $this->createNotFoundException('No products');
        }

        $categories = $this->categoryRepository->findAll();

        return $this->render('products/index.html.twig', ['products' => $products, 'categories' => $categories]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/category/{name}', name: 'app_product_category')]
    public function showByCategory(string $name): Response
    {
        $categories = $this->categoryRepository->findAll();
        $category = $this->categoryRepository->findByName(['name' => $name]);

        $categoryProducts = $category->getProducts();

        return $this->render('products/product_category.html.twig', ['categoryProducts' => $categoryProducts, 'categories' => $categories]);
    }

    #[Route('product/{id}', name: 'app_product_id')]
    public function show(Product $product): Response
    {
        return $this->render('products/show.html.twig' , ['product' => $product]);
    }
}
