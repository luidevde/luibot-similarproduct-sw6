<?php

namespace LuidevRecommendSimilarProducts\Service;

use GuzzleHttp\Exception\GuzzleException;
use LuidevRecommendSimilarProducts\Helper\SimilarProductHelper;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class SimilarProductService
{
    private SalesChannelDataService $salesChannelDataService;
    private ProductDataService $productDataService;
    private AbstractSalesChannelContextFactory $salesChannelContextFactory;
    private ApiService $apiService;
    private SimilarProductHelper $similarProductHelper;
    private LoggerInterface $logger;
    private PluginConfigService $pluginConfigService;

    public function __construct(
        SalesChannelDataService $salesChannelDataService,
        ProductDataService $productDataService,
        AbstractSalesChannelContextFactory $salesChannelContextFactory,
        ApiService $apiService,
        SimilarProductHelper $similarProductHelper,
        LoggerInterface $logger,
        PluginConfigService $pluginConfigService
    ) {
        $this->salesChannelDataService = $salesChannelDataService;
        $this->productDataService = $productDataService;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
        $this->apiService = $apiService;
        $this->similarProductHelper = $similarProductHelper;
        $this->logger = $logger;
        $this->pluginConfigService = $pluginConfigService;
    }

    public function prepareAndSendProductsToSimilarProcess(): void
    {
        $salesChannels = $this->salesChannelDataService->findAllActiveSalesChannels(Context::createDefaultContext());
        /** @var SalesChannelEntity $salesChannel */
        foreach ($salesChannels as $salesChannel) {
            if ($salesChannel->getType() === null
                || $salesChannel->getType()->getIconName() !== "regular-storefront"
                || empty($this->pluginConfigService->getConfigByName('active', $salesChannel->getId()))
            ) {
                continue;
            }

            $salesChannelId = $salesChannel->getId();
            $salesChannelContext = $this->salesChannelContextFactory->create(
                '',
                $salesChannelId,
                [SalesChannelContextService::LANGUAGE_ID => $salesChannel->getLanguageId()]
            );

            $products = $this->productDataService->findSalesChannelProducts($salesChannelId, $salesChannelContext);

            $error = false;
            foreach ($products as $product) {
                $preparedProduct = $this->similarProductHelper->buildSimilarProductData($product);
                if (empty($preparedProduct)) {
                    continue;
                }

                // Send to Api
                try {
                    $this->apiService->request('POST', '/product', $preparedProduct, $salesChannelId);
                } catch (GuzzleException|\JsonException $e) {
                    $this->logger->error($e->getMessage());
                    $error = true;
                }
            }

            if (empty($error)) {
                try {
                    $this->apiService->request('POST', '/trigger/product/process', ['process' => true], $salesChannelId);
                } catch (GuzzleException|\JsonException $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }
}