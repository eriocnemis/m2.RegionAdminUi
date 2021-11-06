<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionAdminUi\Model\Region;

use Magento\Framework\Exception\LocalizedException;
use Eriocnemis\RegionApi\Api\Data\RegionInterface;

/**
 * Region hydrator pool
 */
class HydratorPool implements HydratorInterface
{
    /**
     * @var HydratorInterface[]
     */
    protected $hydrators;

    /**
     * Initialize hydrator
     *
     * @param HydratorInterface[] $hydrators
     */
    public function __construct(
        array $hydrators = []
    ) {
        foreach ($hydrators as $hydrator) {
            if (!$hydrator instanceof HydratorInterface) {
                throw new LocalizedException(
                    __('Region hydrator must implement %1.', HydratorInterface::class)
                );
            }
        }
        $this->hydrators = $hydrators;
    }

    /**
     * Hydrate region attribute values
     *
     * @param RegionInterface $region
     * @param mixed[] $data
     * @return void
     */
    public function hydrate(RegionInterface $region, array $data): void
    {
        foreach ($this->hydrators as $hydrator) {
            $hydrator->hydrate($region, $data);
        }
    }
}
