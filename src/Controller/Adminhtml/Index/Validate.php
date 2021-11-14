<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionAdminUi\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Eriocnemis\Core\Exception\ResolveExceptionInterface;
use Eriocnemis\RegionApi\Api\Data\RegionInterface;
use Eriocnemis\RegionApi\Api\ValidateRegionInterface;
use Eriocnemis\RegionAdminUi\Api\ResolveRegionInterface;

/**
 * Validate region
 */
class Validate extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Eriocnemis_Region::region_edit';

    /**
     * Action name constant
     */
    const ACTION_NAME = 'validate';

    /**
     * @var ResolveRegionInterface
     */
    private $resolveRegion;

    /**
     * @var ValidateRegionInterface
     */
    private $validateRegion;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var ResolveExceptionInterface
     */
    private $resolveException;

    /**
     * Initialize controller
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ResolveRegionInterface $resolveRegion
     * @param ValidateRegionInterface $validateRegion
     * @param ResolveExceptionInterface $resolveException
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ResolveRegionInterface $resolveRegion,
        ValidateRegionInterface $validateRegion,
        ResolveExceptionInterface $resolveException
    ) {
        $this->resolveRegion = $resolveRegion;
        $this->validateRegion = $validateRegion;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resolveException = $resolveException;

        parent::__construct(
            $context
        );
    }

    /**
     * Validate region
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $response = ['error' => true];
        $data = $this->getRequest()->getPost('region');
        $regionId = $data[RegionInterface::REGION_ID] ?? null;

        try {
            $region = $this->resolveRegion->execute($regionId, $data);
            $this->validateRegion->execute($region);
            $response = ['error' => false];
        } catch (\Exception $e) {
            $this->resolveException->execute($e, self::ACTION_NAME);
            $response['messages'] = [];
            foreach ($this->messageManager->getMessages(true)->getErrors() as $message) {
                $response['messages'][] = $message->getText();
            }
        }
        return $this->resultJsonFactory->create()->setData($response);
    }
}
