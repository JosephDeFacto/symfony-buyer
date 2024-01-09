<?php

namespace App\Api\Service;

use App\Entity\Product;
use App\Api\ApiProductHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductService
{
    public HttpClientInterface $client;
    public ApiProductHelper $apiProductHelper;

    public EntityManagerInterface $entityManager;

    public function __construct(HttpClientInterface $dummyProductJsonApiClient, ApiProductHelper $apiProductHelper, EntityManagerInterface $entityManager)
    {
        $this->client = $dummyProductJsonApiClient;
        $this->apiProductHelper = $apiProductHelper;
        $this->entityManager = $entityManager;
    }

    public function save(): void
    {
        $products = $this->apiProductHelper->apiRequest();

        foreach ($products['products'] as $p) {

            $product = new Product();
            $product->setId((int)$p['id']);
            $product->setName((string)$p['title']);
            $product->setDescription($p['description']);
            $product->setPrice($p['price']);
            $product->setStockQuantity($p['stock']);

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