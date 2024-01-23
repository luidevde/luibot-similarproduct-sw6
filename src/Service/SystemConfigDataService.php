<?php

namespace LuidevRecommendSimilarProducts\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Util\Json;

class SystemConfigDataService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function updatePluginConfigInterval(int $interval): void
    {
        try {
            $this->connection->update(
                'system_config',
                [
                    'configuration_value' => Json::encode(['_value' => $interval]),
                ],
                [
                    'configuration_key' => 'LuidevRecommendSimilarProducts.config.interval',
                ]
            );
        } catch (Exception $e) {
        }
    }
}