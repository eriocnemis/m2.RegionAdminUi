<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionAdminUi\Model\Region;

use Eriocnemis\RegionApi\Api\Data\RegionInterface;

/**
 * Format the output result
 */
class OutputProcessor
{
    /**
     * Format the output result
     *
     * @param RegionInterface $region
     * @param mixed[] $data
     * @return mixed[]
     */
    public function execute(RegionInterface $region, array $data)
    {
        return array_map([$this, 'updateValue'], $data);
    }

    /**
     * Modify data value
     *
     * @param mixed $value
     * @return mixed
     */
    public function updateValue($value)
    {
        // for proper work of form and grid (for example for Yes/No properties)
        if (is_numeric($value)) {
            $value = (string)$value;
        } elseif (is_bool($value)) {
            $value = (string)(int)$value;
        }
        return $value;
    }
}
