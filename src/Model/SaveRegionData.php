<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionAdminUi\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Eriocnemis\RegionApi\Api\Data\RegionInterface;
use Eriocnemis\RegionApi\Api\SaveRegionInterface;
use Eriocnemis\RegionAdminUi\Api\ResolveRegionInterface;
use Eriocnemis\RegionAdminUi\Api\SaveRegionDataInterface;

/**
 * Save region data
 *
 * @api
 */
class SaveRegionData implements SaveRegionDataInterface
{
    /**
     * @var SaveRegionInterface
     */
    private $saveRegion;

    /**
     * @var ResolveRegionInterface
     */
    private $resolveRegion;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * Initialize controller
     *
     * @param SaveRegionInterface $saveRegion
     * @param ResolveRegionInterface $resolveRegion
     * @param DataPersistorInterface $dataPersistor
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        SaveRegionInterface $saveRegion,
        ResolveRegionInterface $resolveRegion,
        DataPersistorInterface $dataPersistor,
        MessageManagerInterface $messageManager
    ) {
        $this->saveRegion = $saveRegion;
        $this->resolveRegion = $resolveRegion;
        $this->dataPersistor = $dataPersistor;
        $this->messageManager = $messageManager;
    }

    /**
     * Save data
     *
     * @param RequestInterface $request
     * @return RegionInterface
     * @throws LocalizedException
     */
    public function execute(RequestInterface $request): RegionInterface
    {
        $data = $request->getPost('region');
        if (empty($data)) {
            throw new LocalizedException(
                __('Wrong request.')
            );
        }

        $regionId = $data[RegionInterface::REGION_ID] ?? null;
        $this->dataPersistor->set('eriocnemis_region', $data);

        $region = $this->saveRegion->execute(
            $this->resolveRegion->execute($regionId, $data)
        );

        $this->messageManager->addSuccessMessage(
            (string)__('The Region has been saved.')
        );

        return $region;
    }
}
