<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionAdminUi\Api;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Eriocnemis\RegionApi\Api\Data\RegionInterface;

/**
 * Save data
 *
 * @api
 */
interface SaveRegionDataInterface
{
    /**
     * Save data
     *
     * @param RequestInterface $request
     * @return RegionInterface
     * @throws LocalizedException
     */
    public function execute(RequestInterface $request): RegionInterface;
}
