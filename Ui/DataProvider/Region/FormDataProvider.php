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
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Eriocnemis\RegionApi\Api\GetRegionByIdInterface;

/**
 * Data provider for admin export job form
 *
 * @api
 */
class FormDataProvider extends DataProvider
{
    /**
     * @var GetRegionByIdInterface
     */
    private $getRegionById;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var PoolInterface
     */
    private $modifierPool;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

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
     * @param GetRegionByIdInterface $getRegionById
     * @param DataPersistorInterface $dataPersistor
     * @param HydratorInterface $hydrator
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
        GetRegionByIdInterface $getRegionById,
        DataPersistorInterface $dataPersistor,
        HydratorInterface $hydrator,
        PoolInterface $modifierPool,
        array $meta = [],
        array $data = []
    ) {
        $this->getRegionById = $getRegionById;
        $this->dataPersistor = $dataPersistor;
        $this->modifierPool = $modifierPool;
        $this->hydrator = $hydrator;

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
        $regionId = $this->getRegionId();
        if (!isset($this->data[$regionId])) {
            $this->data[$regionId]['region'] = $this->modifyData($this->loadData($regionId));
        }
        return $this->data;
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
     * Retrieve region id
     *
     * @return int|null
     */
    private function getRegionId(): ?int
    {
        $regionId = $this->request->getParam($this->getRequestFieldName());
        return $regionId ? (int)$regionId : null;
    }

    /**
     * Retrieve region data
     *
     * @param int|null $regionId
     * @return mixed[]
     */
    private function loadData($regionId): array
    {
        $data = $this->dataPersistor->get('eriocnemis_region') ?: [];
        if (null !== $regionId) {
            $region = $this->getRegionById->execute($regionId);
            $data = array_map([$this, 'updateValue'], $this->hydrator->extract($region));
        }
        $this->dataPersistor->clear('eriocnemis_region');

        return $data;
    }

    /**
     * Modify data value
     *
     * @param mixed $value
     * @return mixed
     */
    public function updateValue($value)
    {
        if (is_numeric($value)) {
            // for proper work of form and grid (for example for Yes/No properties)
            $value = (string)$value;
        }
        return $value;
    }

    /**
     * Retrieve modifier data
     *
     * @param  mixed[] $data
     * @return mixed[]
     */
    private function modifyData(array $data): array
    {
        /** @var ModifierInterface $modifier */
        foreach ($this->modifierPool->getModifiersInstances() as $modifier) {
            $data = $modifier->modifyData($data);
        }
        return $data;
    }
}
