<?php declare(strict_types=1);

namespace LuidevRecommendSimilarProducts\ScheduledTask;

use LuidevRecommendSimilarProducts\Service\SimilarProductService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

class ProductProcessTaskHandler extends ScheduledTaskHandler
{
    private SimilarProductService $similarProductService;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        SimilarProductService $similarProductService
    )
    {
        parent::__construct($scheduledTaskRepository);
        $this->similarProductService = $similarProductService;
    }

    public static function getHandledMessages(): iterable
    {
        return [ProductProcessTask::class];
    }

    public function run(): void
    {
        $this->similarProductService->prepareAndSendProductsToSimilarProcess();
    }
}