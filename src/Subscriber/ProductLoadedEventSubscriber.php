<?php

namespace LuidevRecommendSimilarProducts\Subscriber;

use GuzzleHttp\Exception\GuzzleException;
use LuidevRecommendSimilarProducts\Service\ApiService;
use LuidevRecommendSimilarProducts\Service\PluginConfigService;
use LuidevRecommendSimilarProducts\Service\ProductDataService;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductLoadedEventSubscriber implements EventSubscriberInterface
{
    private ApiService $apiService;
    private ProductDataService $productDataService;
    private PluginConfigService $pluginConfigService;

    public function __construct(
        ApiService $apiService,
        ProductDataService $productDataService,
        PluginConfigService $pluginConfigService
    ) {
        $this->apiService = $apiService;
        $this->productDataService = $productDataService;
        $this->pluginConfigService = $pluginConfigService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'assignSimilarProductToDetailPage',
        ];
    }

    public function assignSimilarProductToDetailPage(
        ProductPageLoadedEvent $event
    ): void {
        $salesChannelId = $event->getSalesChannelContext()->getSalesChannelId();

        if (!$this->pluginConfigService->getConfigByName('active', $salesChannelId)) {
            return;
        }

        $page = $event->getPage();
        $product = $page->getProduct();
        $productId = $product->getParentId() ?? $product->getId();
        $similarProductIds = [];

        try {
            $similarProductsResponse = $this->apiService->request(
                'GET',
                '/product/similarity/product/'.$productId,
                ['event' => 'similar'],
                $salesChannelId
            );

            $similarProducts = json_decode(
                $similarProductsResponse->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            $similarProductIds = array_column($similarProducts, 'similarPi');
        } catch (GuzzleException|\JsonException $e) {
        }

        if (empty($similarProductIds)) {
            return;
        }

        $limit = $this->pluginConfigService->getConfigByName(
            'limit',
            $event->getSalesChannelContext()->getSalesChannelId()
        );

        $products = $this->productDataService->findProductsByIds(
            $similarProductIds,
            $event->getSalesChannelContext()
        );

        if (empty($products)) {
            return;
        }

        $products = array_replace(array_flip($similarProductIds), $products);
        $page->assign(['luiSimilarProducts' => array_slice(array_values($products), 0, $limit)]);
    }
}
