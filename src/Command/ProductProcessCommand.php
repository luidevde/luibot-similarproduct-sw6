<?php

namespace LuidevRecommendSimilarProducts\Command;

use GuzzleHttp\Exception\GuzzleException;
use LuidevRecommendSimilarProducts\Helper\SimilarProductHelper;
use LuidevRecommendSimilarProducts\Service\ApiService;
use LuidevRecommendSimilarProducts\Service\ProductDataService;
use LuidevRecommendSimilarProducts\Service\SalesChannelDataService;
use LuidevRecommendSimilarProducts\Service\SimilarProductService;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProductProcessCommand extends Command
{
    private SimilarProductService $similarProductService;

    public function __construct(
       SimilarProductService $similarProductService
    ) {
        parent::__construct();
        $this->similarProductService = $similarProductService;
    }

    protected function configure(): void
    {
        $this->setName('lui:process:product');
        $this->setDescription('Product process');
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->similarProductService->prepareAndSendProductsToSimilarProcess();
        return Command::SUCCESS;
    }
}