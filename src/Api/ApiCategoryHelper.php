<?php

namespace App\Api;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiCategoryHelper implements ApiHelperInterface
{
    public HttpClientInterface $client;

    public function __construct(HttpClientInterface $dummyCategoryJsonApiClient)
    {
        $this->client = $dummyCategoryJsonApiClient;
    }
    public function apiRequest(): array
    {
        $response = $this->client->request('GET', '');

        return $response->toArray();
    }
}