<?php

namespace App\Api\Service;

use App\Api\ApiCategoryHelper;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CategoryService
{
    public HttpClientInterface $client;
    public EntityManagerInterface $entityManager;
    public ApiCategoryHelper $apiCategoryHelper;

    public function __construct(HttpClientInterface $dummyCategoryJsonApiClient, EntityManagerInterface $entityManager, ApiCategoryHelper $apiCategoryHelper)
    {
        $this->client = $dummyCategoryJsonApiClient;
        $this->entityManager = $entityManager;
        $this->apiCategoryHelper = $apiCategoryHelper;
    }

    public function save(): void
    {
        $categories = $this->apiCategoryHelper->apiRequest();

        foreach ($categories as $c) {
            $category = new Category();
            $category->setName($c);

            $this->entityManager->persist($category);

            $this->entityManager->flush();
        }
    }

    public function delete(): void
    {
        $query = $this->entityManager->createQuery('DELETE FROM App\Entity\Category c');
        $query->execute();
    }
}