<?php

namespace App\Service;

use App\Entity\Product;
use App\Helper\ApiHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductService
{
    public HttpClientInterface $client;
    public ApiHelper $apiHelper;

    public EntityManagerInterface $entityManager;

    public function __construct(HttpClientInterface $dummyJsonApiClient, ApiHelper $apiHelper, EntityManagerInterface $entityManager)
    {
        $this->client = $dummyJsonApiClient;
        $this->apiHelper = $apiHelper;
        $this->entityManager = $entityManager;
    }

    public function save(): void
    {
        $data = $this->apiHelper->apiRequest();

        foreach ($data['products'] as $p) {

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