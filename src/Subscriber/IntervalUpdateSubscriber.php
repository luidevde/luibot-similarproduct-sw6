<?php

namespace LuidevRecommendSimilarProducts\Subscriber;

use LuidevRecommendSimilarProducts\Service\ScheduledTaskDataService;
use LuidevRecommendSimilarProducts\Service\SystemConfigDataService;
use Shopware\Core\System\SystemConfig\Event\SystemConfigChangedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IntervalUpdateSubscriber implements EventSubscriberInterface
{
    public const MIN_CRON_INTERVAL = 86400;
    public const INTERVAL_CONFIG_KEY = 'LuidevRecommendSimilarProducts.config.interval';
    private ScheduledTaskDataService $scheduledTaskDataService;
    private SystemConfigDataService $systemConfigDataService;

    public function __construct(
        ScheduledTaskDataService $scheduledTaskDataService,
        SystemConfigDataService $systemConfigDataService
    ) {
        $this->scheduledTaskDataService = $scheduledTaskDataService;
        $this->systemConfigDataService = $systemConfigDataService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SystemConfigChangedEvent::class => 'updateSystemConfig',
        ];
    }

    public function updateSystemConfig(SystemConfigChangedEvent $event): void
    {
        $configKey = $event->getKey();

        if ($configKey !== self::INTERVAL_CONFIG_KEY) {
            return;
        }

        $value = $event->getValue();
        if ($value < self::MIN_CRON_INTERVAL) {
            $value = self::MIN_CRON_INTERVAL;
        }

        $this->systemConfigDataService->updatePluginConfigInterval($value);
        $this->scheduledTaskDataService->updateSimilarProductsTaskInterval($value);
    }
}