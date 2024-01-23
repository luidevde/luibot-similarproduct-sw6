<?php

namespace LuidevRecommendSimilarProducts\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class SalesChannelDataService
{
    private EntityRepository $salesChannelRepository;

    public function __construct(EntityRepository $salesChannelRepository)
    {
        $this->salesChannelRepository = $salesChannelRepository;
    }

    public function findAllActiveSalesChannels(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('active', true)
        );
        $criteria->addAssociation('type');
        $criteria->addAssociation('domains');

        return $this->salesChannelRepository->search($criteria, $context)->getElements();
    }
}