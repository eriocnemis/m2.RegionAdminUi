<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionAdminUi\Model\Region;

use Eriocnemis\RegionApi\Api\Data\RegionInterface;

/**
 * Extension point for base hydration of region
 *
 * @api
 */
interface HydratorInterface
{
    /**
     * Hydrate region attribute values
     *
     * @param RegionInterface $region
     * @param mixed[] $data
     * @return void
     */
    public function hydrate(RegionInterface $region, array $data): void;
}
