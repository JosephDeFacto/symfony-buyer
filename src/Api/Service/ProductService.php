<?php

namespace App\Api\Service;

use App\Entity\Product;
use App\Api\ApiProductHelper;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductService
{
    public HttpClientInterface $client;
    public ApiProductHelper $apiProductHelper;

    public EntityManagerInterface $entityManager;

    public CategoryRepository $categoryRepository;

    public function __construct(HttpClientInterface $dummyProductJsonApiClient, ApiProductHelper $apiProductHelper, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository)
    {
        $this->client = $dummyProductJsonApiClient;
        $this->apiProductHelper = $apiProductHelper;
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
    }

    public function save(): void
    {
        $products = $this->apiProductHelper->apiRequest();

        foreach ($products['products'] as $p) {

            $category = $this->categoryRepository->findOneBy(['name' => $p['category']]);

            $product = new Product();
            $product->setId((int)$p['id']);
            $product->setName((string)$p['title']);
            $product->setDescription($p['description']);
            $product->setPrice($p['price']);
            $product->setStockQuantity($p['stock']);
            $product->setCategory($category);

            $this->entityManager->persist($product);

            $this->entityManager->flush();
        }
    }

    public function delete(): void
    {
        $query = $this->entityManager->createQuery('DELETE FROM App\Entity\Product p');
        $query->execute();
    }
}