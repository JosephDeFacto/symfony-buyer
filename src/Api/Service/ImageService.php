<?php

namespace App\Api\Service;

use App\Api\ApiProductHelper;
use App\Entity\Image;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImageService
{
    public HttpClientInterface $client;
    public ApiProductHelper $apiProductHelper;
    public EntityManagerInterface $entityManager;
    public CategoryRepository $categoryRepository;
    public ProductRepository $productRepository;

    public function __construct(HttpClientInterface $dummyProductJsonApiClient, ApiProductHelper $apiProductHelper, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, ProductRepository $productRepository)
    {
        $this->client = $dummyProductJsonApiClient;
        $this->apiProductHelper = $apiProductHelper;
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    public function save(): void
    {
        $products = $this->apiProductHelper->apiRequest();

        foreach ($products['products'] as $p) {
            $product = $this->productRepository->findOneBy(['name' => $p['title']]);

            foreach ($p['images'] as $img) {
                $image = new Image();
                $image->setFilename($img);
                $product->addImage($image);
                $this->entityManager->persist($image);
            }
            $this->entityManager->persist($product);
        }
        $this->entityManager->flush();
    }

    public function delete(): void
    {
        $query = $this->entityManager->createQuery('DELETE FROM App\Entity\Image i');
        $query->execute();
    }
}