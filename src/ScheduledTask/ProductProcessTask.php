<?php declare(strict_types=1);

namespace LuidevRecommendSimilarProducts\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class ProductProcessTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'lui.similar_products.task';
    }

    public static function getDefaultInterval(): int
    {
        return 86400;
    }
}