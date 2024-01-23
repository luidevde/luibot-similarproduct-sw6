<?php

namespace LuidevRecommendSimilarProducts\Api\Controller;

use LuidevRecommendSimilarProducts\Service\ApiService;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * @Route(defaults={"_routeScope" = {"administration"}})
 */
class LuibotApiController
{
    private ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @Route(path="/api/_action/luibot/api/login/verify")
     */
    public function check(RequestDataBag $dataBag): JsonResponse
    {
        $success = false;
        $username = $dataBag->get('LuidevRecommendSimilarProducts.config.username');
        $apiKey = $dataBag->get('LuidevRecommendSimilarProducts.config.apiKey');
        $apiUrl = $dataBag->get('LuidevRecommendSimilarProducts.config.apiUrl');

        try {
            $body = json_encode([
                'username' => $username,
                'apiKey'   => $apiKey,
            ], JSON_THROW_ON_ERROR);

            $request = $this->apiService->createRequestForToken($body, $apiUrl . '/login');
            $response = $this->apiService->getResponse($request);

            if ($response->getStatusCode() === 200) {
                $success = true;
            }
        } catch (\Exception $e) {}

        return new JsonResponse(['success' => $success]);
    }
}