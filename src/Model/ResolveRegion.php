<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionAdminUi\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Eriocnemis\RegionAdminUi\Api\ResolveRegionInterface;
use Eriocnemis\RegionApi\Api\Data\RegionInterfaceFactory;
use Eriocnemis\RegionApi\Api\Data\RegionInterface;
use Eriocnemis\RegionApi\Api\GetRegionByIdInterface;

/**
 * Resolve region data
 *
 * @api
 */
class ResolveRegion implements ResolveRegionInterface
{
    /**
     * @var RegionInterfaceFactory
     */
    private $factory;

    /**
     * @var GetRegionByIdInterface
     */
    private $getRegionById;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Initialize provider
     *
     * @param RegionInterfaceFactory $factory
     * @param GetRegionByIdInterface $getRegionById
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        RegionInterfaceFactory $factory,
        GetRegionByIdInterface $getRegionById,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->factory = $factory;
        $this->getRegionById = $getRegionById;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Resolve region
     *
     * @param int|null $regionId
     * @param mixed[] $data
     * @return RegionInterface
     * @throws NoSuchEntityException
     */
    public function execute($regionId, array $data): RegionInterface
    {
        /** @var RegionInterface $region */
        $region = null !== $regionId
            ? $this->getRegionById->execute((int)$regionId)
            : $this->factory->create();

        $this->dataObjectHelper->populateWithArray($region, $data, RegionInterface::class);

        return $region;
    }
}
