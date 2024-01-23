<?php

namespace LuidevRecommendSimilarProducts\Service;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Grouping\FieldGrouping;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ProductDataService
{
    private SalesChannelRepository $productRepository;

    public function __construct(SalesChannelRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function findSalesChannelProducts(string $salesChannelId, SalesChannelContext $context): array
    {
        $criteria = new Criteria();
        $criteria->addAssociation('cover');
        $criteria->addAssociation('visibilities');
        $criteria->addAssociation('categories');
        $criteria->addFilter(new EqualsFilter('active', 1));
        $criteria->addFilter(new EqualsFilter('parentId', null));
        $criteria->addFilter(new EqualsFilter('product.visibilities.salesChannelId', $salesChannelId));

        return $this->productRepository->search($criteria, $context)->getElements();
    }

    public function findProductsByIds(array $productIds, SalesChannelContext $context, int $limit = 10): array
    {
        $criteria = new Criteria();
        $criteria->addAssociation('cover');
        $criteria->addAssociation('options.group');
        $criteria->addAssociation('manufacturer');

        $criteria->addFilter(
            new EqualsAnyFilter('id', $productIds)
        );

        $criteria->addGroupField(new FieldGrouping('id'));

        return $this->productRepository->search($criteria, $context)->getElements();
    }
}