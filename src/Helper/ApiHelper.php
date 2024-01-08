<?php

namespace App\Helper;

use Symfony\Contracts\HttpClient\HttpClientInterface;
class ApiHelper
{
    public HttpClientInterface $client;

    public function __construct(HttpClientInterface $dummyJsonApiClient)
    {
        $this->client = $dummyJsonApiClient;
    }

    public function apiRequest(): array
    {
        $response = $this->client->request('GET', '');

        return $response->toArray();
    }
}