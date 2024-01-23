<?php

namespace LuidevRecommendSimilarProducts\Storefront\Controller;


use GuzzleHttp\Exception\GuzzleException;
use LuidevRecommendSimilarProducts\Service\ApiService;
use LuidevRecommendSimilarProducts\Service\PluginConfigService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class RecommendSimilarProductsController extends StorefrontController
{
    private ApiService $apiService;
    private PluginConfigService $pluginConfigService;

    public function __construct(ApiService $apiService, PluginConfigService $pluginConfigService)
    {
        $this->apiService = $apiService;
        $this->pluginConfigService = $pluginConfigService;
    }

    /**
     * @Route("/luidev/statistics/click",
     *     name="frontend.luidev.statistics.click",
     *     defaults={"XmlHttpRequest": true},
     *     methods={"POST"})
     */
    public function click(SalesChannelContext $context, Request $request): JsonResponse
    {
        if (empty($this->pluginConfigService->getConfigByName('active', $context->getSalesChannelId()))) {
            return new JsonResponse(['success' => false, 'message' => 'The plugin is not activated for this sales channel']);
        }

        $productId = $request->request->get('productId');
        if ($productId === null) {
            return new JsonResponse(['success' => false, 'message' => 'Product id ist missing'], 400);
        }

        try {
            $this->apiService->request(
                'POST',
                '/statistics/click',
                ['productId' => $productId],
                $context->getSalesChannelId()
            );
        } catch (GuzzleException $exception) {
            return new JsonResponse(['success' => false], $exception->getCode());
        } catch (\JsonException $e) {
            return new JsonResponse(['success' => false], $e->getCode());
        }

        return new JsonResponse(['success' => true]);
    }
}