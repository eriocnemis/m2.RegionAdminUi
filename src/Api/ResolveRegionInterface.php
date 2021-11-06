<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionAdminUi\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Eriocnemis\RegionApi\Api\Data\RegionInterface;

/**
 * Resolve region data interface
 *
 * @api
 */
interface ResolveRegionInterface
{
    /**
     * Resolve region
     *
     * @param int|null $regionId
     * @param mixed[] $data
     * @return RegionInterface
     * @throws NoSuchEntityException
     */
    public function execute($regionId, array $data): RegionInterface;
}
