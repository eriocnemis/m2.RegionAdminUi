<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionAdminUi\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Eriocnemis\Core\Exception\ResolveExceptionInterface;
use Eriocnemis\RegionApi\Api\Data\RegionInterface;
use Eriocnemis\RegionAdminUi\Api\SaveRegionDataInterface;

/**
 * Save controller
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Eriocnemis_Region::region_edit';

    /**
     * Action name constant
     */
    const ACTION_NAME = 'save';

    /**
     * @var SaveRegionDataInterface
     */
    private $saveRegionData;

    /**
     * @var ResolveExceptionInterface
     */
    private $resolveException;

    /**
     * Initialize controller
     *
     * @param Context $context
     * @param SaveRegionDataInterface $saveRegionData
     * @param ResolveExceptionInterface $resolveException
     */
    public function __construct(
        Context $context,
        SaveRegionDataInterface $saveRegionData,
        ResolveExceptionInterface $resolveException
    ) {
        $this->saveRegionData = $saveRegionData;
        $this->resolveException = $resolveException;

        parent::__construct(
            $context
        );
    }

    /**
     * Save region
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $regionId = (int)$this->getRequest()->getPost(RegionInterface::REGION_ID);
        /** @var Redirect $result */
        $result = $this->resultRedirectFactory->create();

        try {
            $region = $this->saveRegionData->execute($this->getRequest());
            return $this->resolveResult($result, (int)$region->getId());
        } catch (\Exception $e) {
            $this->resolveException->execute($e, self::ACTION_NAME);
        }
        return $this->resolveFailureResult($result, $regionId);
    }

    /**
     * Resolve success result
     *
     * @param Redirect $result
     * @param int $regionId
     * @return ResultInterface
     */
    private function resolveResult(Redirect $result, int $regionId): ResultInterface
    {
        return empty($this->getRequest()->getParam('back'))
            ? $result->setPath('*/*/index')
            : $result->setPath('*/*/edit', $this->getParams($regionId));
    }

    /**
     * Resolve failure result
     *
     * @param Redirect $result
     * @param int|null $regionId
     * @return ResultInterface
     */
    private function resolveFailureResult(Redirect $result, int $regionId = null): ResultInterface
    {
        return empty($regionId)
            ? $result->setPath('*/*/new')
            : $result->setPath('*/*/edit', $this->getParams($regionId));
    }

    /**
     * Retrieve params
     *
     * @param int $regionId
     * @return mixed[]
     */
    private function getParams(int $regionId): array
    {
        return [
            RegionInterface::REGION_ID => $regionId,
            '_current' => true
        ];
    }
}
