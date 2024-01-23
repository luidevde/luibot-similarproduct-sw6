<?php

namespace LuidevRecommendSimilarProducts\Components\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class GuzzleFactory
{
    public function createClient(array $config = []): ClientInterface
    {
        return new Client($config);
    }
}