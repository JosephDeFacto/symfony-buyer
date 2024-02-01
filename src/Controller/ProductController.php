<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    public ProductRepository $productRepository;
    public CategoryRepository $categoryRepository;

    public PaginatorInterface $paginator;


    public function __construct(ProductRepository $productRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->paginator = $paginator;

    }
    #[Route('/', name: 'app_product')]
    public function index(Request $request): Response
    {
        $products = $this->productRepository->findAll();

        if (!$products) {
            throw $this->createNotFoundException('No products');
        }

        $pagination = $this->paginator->paginate($products, $request->query->getInt('page', 1), 10);
        $pagination->setCustomParameters(['align' => 'center', 'size' => 'medium', 'style' => 'bottom']);


        $categories = $this->categoryRepository->findAll();

        return $this->render('products/index.html.twig', ['categories' => $categories, 'pagination' => $pagination]);
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
