<?php

namespace App\Api;

use Symfony\Contracts\HttpClient\HttpClientInterface;
class ApiProductHelper implements ApiHelperInterface
{
    public HttpClientInterface $client;

    public function __construct(HttpClientInterface $dummyProductJsonApiClient)
    {
        $this->client = $dummyProductJsonApiClient;
    }

    public function apiRequest(): array
    {
        $response = $this->client->request('GET', '');

        return $response->toArray();
    }
}