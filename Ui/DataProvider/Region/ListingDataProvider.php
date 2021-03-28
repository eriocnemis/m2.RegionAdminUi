<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionAdminUi\Ui\DataProvider\Region;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Ui\DataProvider\SearchResultFactory;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Eriocnemis\RegionApi\Api\GetRegionListInterface;
use Eriocnemis\RegionApi\Api\Data\RegionInterface;

/**
 * Data provider for admin grid
 *
 * @api
 */
class ListingDataProvider extends DataProvider
{
    /**
     * @var GetRegionListInterface
     */
    private $getRegionList;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var PoolInterface
     */
    private $modifierPool;

    /**
     * Initialize provider
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param GetRegionListInterface $getRegionList
     * @param SearchResultFactory $searchResultFactory
     * @param PoolInterface $modifierPool
     * @param mixed[] $meta
     * @param mixed[] $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        GetRegionListInterface $getRegionList,
        SearchResultFactory $searchResultFactory,
        PoolInterface $modifierPool,
        array $meta = [],
        array $data = []
    ) {
        $this->getRegionList = $getRegionList;
        $this->searchResultFactory = $searchResultFactory;
        $this->modifierPool = $modifierPool;

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * Retrieve data
     *
     * @return mixed[]
     */
    public function getData()
    {
        $data = parent::getData();
        /** @var ModifierInterface $modifier */
        foreach ($this->modifierPool->getModifiersInstances() as $modifier) {
            $data = $modifier->modifyData($data);
        }
        return $data;
    }

    /**
     * Retrieve meta data
     *
     * @return mixed[]
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        /** @var ModifierInterface $modifier */
        foreach ($this->modifierPool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }
        return $meta;
    }

    /**
     * Retrieve search result
     *
     * @return SearchResultInterface
     */
    public function getSearchResult()
    {
        $searchCriteria = $this->getSearchCriteria();
        $result = $this->getRegionList->execute($searchCriteria);

        return $this->searchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $searchCriteria,
            RegionInterface::REGION_ID
        );
    }
}
