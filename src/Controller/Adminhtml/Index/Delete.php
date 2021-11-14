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
use Magento\Framework\App\Action\HttpPostActionInterface;
use Eriocnemis\Core\Exception\ResolveExceptionInterface;
use Eriocnemis\RegionApi\Api\Data\RegionInterface;
use Eriocnemis\RegionApi\Api\DeleteRegionByIdInterface;

/**
 * Delete controller
 */
class Delete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Eriocnemis_Region::region_delete';

    /**
     * Action name constant
     */
    const ACTION_NAME = 'delete';

    /**
     * @var DeleteRegionByIdInterface
     */
    private $deleteRegionById;

    /**
     * @var ResolveExceptionInterface
     */
    private $resolveException;

    /**
     * Initialize controller
     *
     * @param Context $context
     * @param DeleteRegionByIdInterface $deleteRegionById
     * @param ResolveExceptionInterface $resolveException
     */
    public function __construct(
        Context $context,
        DeleteRegionByIdInterface $deleteRegionById,
        ResolveExceptionInterface $resolveException
    ) {
        $this->deleteRegionById = $deleteRegionById;
        $this->resolveException = $resolveException;

        parent::__construct(
            $context
        );
    }

    /**
     * Delete specified region
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $regionId = (int)$this->getRequest()->getPost(RegionInterface::REGION_ID);
        /** @var \Magento\Framework\Controller\Result\Redirect $result */
        $result = $this->resultRedirectFactory->create();

        if ($regionId) {
            try {
                $this->deleteRegionById->execute($regionId);
                $this->messageManager->addSuccessMessage(
                    (string)__('The Region has been deleted.')
                );
                return $result->setPath('*/*/index');
            } catch (\Exception $e) {
                $this->resolveException->execute($e, self::ACTION_NAME);
            }
            return $result->setPath('*/*/edit', $this->getParams($regionId));
        }
        return $result->setPath('*/*');
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
