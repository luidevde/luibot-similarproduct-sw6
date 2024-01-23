<?php

namespace LuidevRecommendSimilarProducts\Service;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use LuidevRecommendSimilarProducts\Components\HttpClient\GuzzleFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiService
{
    private ClientInterface $httpClient;
    private PluginConfigService $pluginConfigService;
    private ?string $token = null;
    private ?DateTimeInterface $expiryTime = null;

    public function __construct(
        GuzzleFactory $guzzleFactory,
        PluginConfigService $pluginConfigService
    ) {
        $this->httpClient = $guzzleFactory->createClient(
            [
                'timeout' => 5,
                'connect_timeout' => 5,
            ]
        );
        $this->pluginConfigService = $pluginConfigService;
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function request(string $method, string $uri, ?array $body = null, ?string $salesChannelId = null): ResponseInterface
    {
        $this->getAccessToken($salesChannelId);

        $request = $this->createApiRequest(
            $method,
            $uri,
            $body === null ? null : json_encode($body,JSON_THROW_ON_ERROR)
        );

        return $this->httpClient->send($request);
    }

    public function getResponse(RequestInterface $request): ResponseInterface
    {
        return $this->httpClient->send($request);
    }

    public function createRequestForToken(?string $body, ?string $url = null): Request
    {
        return new Request(
            'POST',
            $url,
            ['Content-Type' => 'application/json'],
            $body
        );
    }

    private function createApiRequest(string $method, string $uri, ?string $body = null): RequestInterface
    {
        return new Request(
            $method,
            $this->pluginConfigService->getConfigByName('apiUrl') . $uri,
            [
                'Authorization' => $this->prepareAuthorizationHeader(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            $body
        );
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    private function getAccessToken(?string $salesChannelId = null): void
    {
        $requestBody = $this->prepareRequestBodyForToken($salesChannelId);
        $response = $this->httpClient->send($this->createRequestForToken($requestBody, $this->prepareAuthTokenUri($salesChannelId)));
        $this->setAccessData(json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @throws \JsonException
     */
    private function prepareRequestBodyForToken(?string $salesChannelId = null)
    {
        return json_encode([
            'username' => $this->pluginConfigService->getConfigByName('username', $salesChannelId),
            'apiKey' => $this->pluginConfigService->getConfigByName('apiKey', $salesChannelId)
        ], JSON_THROW_ON_ERROR);
    }

    private function prepareAuthTokenUri(?string $salesChannelId = null): string
    {
        return $this->pluginConfigService->getConfigByName('apiUrl', $salesChannelId). '/login';
    }

    private function prepareAuthorizationHeader(): string
    {
        return 'Bearer ' . $this->token;
    }

    private function setAccessData(array $body): void
    {
        $this->token = $body['access_token'] ?? null;
        $this->expiryTime = $this->calculateExpiryTime((int)$body['expires_in'] ?? 0);
    }

    private function calculateExpiryTime(int $expiresIn): DateTimeInterface
    {
        $expiryTimestamp = (new DateTime())->getTimestamp() + $expiresIn;
        return (new DateTimeImmutable())->setTimestamp($expiryTimestamp);
    }
}