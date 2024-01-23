<?php

namespace LuidevRecommendSimilarProducts\Service;

use Shopware\Core\System\SystemConfig\SystemConfigService;

class PluginConfigService
{
    private SystemConfigService $configService;

    const PLUGIN_CONFIG_PREFIX = 'LuidevRecommendSimilarProducts.config.';

    public function __construct(SystemConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function getConfigByName(string $configName, ?string $salesChannelId = null)
    {
        return $this->configService->get(self::PLUGIN_CONFIG_PREFIX . $configName, $salesChannelId);
    }
}