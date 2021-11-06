<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionAdminUi\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Eriocnemis\RegionApi\Api\Data\RegionInterface;
use Eriocnemis\RegionApi\Api\GetRegionByIdInterface;

/**
 * Edit controller
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Eriocnemis_Region::region_edit';

    /**
     * @var GetRegionByIdInterface
     */
    private $getRegionById;

    /**
     * Initialize controller
     *
     * @param Context $context
     * @param GetRegionByIdInterface $getRegionById
     */
    public function __construct(
        Context $context,
        GetRegionByIdInterface $getRegionById
    ) {
        $this->getRegionById = $getRegionById;

        parent::__construct(
            $context
        );
    }

    /**
     * Edit model
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $regionId = (int)$this->getRequest()->getParam(RegionInterface::REGION_ID);
        /** @var \Magento\Backend\Model\View\Result\Page $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $result->setActiveMenu('Eriocnemis_Region::geography');

        $title = $result->getConfig()->getTitle();
        $title->prepend((string)__('Geography'));
        $title->prepend((string)__('Regions'));

        try {
            $region = $this->getRegionById->execute($regionId);
            $title->prepend($region->getDefaultName());
        } catch (NoSuchEntityException $e) {
            /** @var \Magento\Framework\Controller\Result\Redirect $result */
            $result = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(
                (string)__('The region with id "%1" does not exist.', $regionId)
            );
            $result->setPath('*/*');
        }

        return $result;
    }
}
