<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionAdminUi\Controller\Adminhtml\Index;

use Psr\Log\LoggerInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validation\ValidationException;
use Eriocnemis\RegionAdminUi\Api\ResolveRegionInterface;
use Eriocnemis\RegionApi\Api\ValidateRegionInterface;
use Eriocnemis\RegionApi\Api\Data\RegionInterface;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Initialize controller
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ResolveRegionInterface $resolveRegion
     * @param ValidateRegionInterface $validateRegion
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ResolveRegionInterface $resolveRegion,
        ValidateRegionInterface $validateRegion,
        LoggerInterface $logger
    ) {
        $this->resolveRegion = $resolveRegion;
        $this->validateRegion = $validateRegion;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;

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
        } catch (ValidationException $e) {
            $response['messages'] = [];
            foreach ($e->getErrors() as $error) {
                $response['messages'][] = $error->getMessage();
            }
        } catch (LocalizedException $e) {
            $response['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $response['message'] = __('We can\'t validate the region right now. Please review the log and try again.');
        }
        return $this->resultJsonFactory->create()->setData($response);
    }
}
