<?php

namespace LuidevRecommendSimilarProducts\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use LuidevRecommendSimilarProducts\ScheduledTask\ProductProcessTask;

class ScheduledTaskDataService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function updateSimilarProductsTaskInterval(int $interval): void
    {
        try {
            $this->connection->update(
                'scheduled_task',
                [
                    'run_interval'         => $interval,
                    'default_run_interval' => $interval,
                ],
                [
                    'name' => ProductProcessTask::getTaskName(),
                ]
            );
        } catch (Exception $e) {
        }
    }
}