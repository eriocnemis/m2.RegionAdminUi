<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\RegionAdminUi\Ui\DataProvider\Region\Modifier\Form;

use Magento\Config\Model\Config\Source\Locale\Country as CountrySource;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Eriocnemis\RegionApi\Api\Data\RegionInterface;

/**
 * General modifier
 *
 * @api
 */
class General implements ModifierInterface
{
    /**
     * Fieldset name
     */
    const FIELDSET = 'general';

    /**
     * @var CountrySource
     */
    private $countrySource;

    /**
     * Initialize modifier
     *
     * @param CountrySource $countrySource
     */
    public function __construct(
        CountrySource $countrySource
    ) {
        $this->countrySource = $countrySource;
    }

    /**
     * Modify form data
     *
     * @param mixed[] $data
     * @return mixed[]
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Modify form meta
     *
     * @param mixed[] $meta
     * @return mixed[]
     */
    public function modifyMeta(array $meta)
    {
        $meta[self::FIELDSET]['children'] = [
            RegionInterface::STATUS => $this->getStatus(),
            RegionInterface::COUNTRY_ID => $this->getCountryId()
        ];
        return $meta;
    }

    /**
     * Retrieve status configuration
     *
     * @return mixed[]
     */
    private function getStatus()
    {
        return ['arguments' => $this->getArguments($this->getStatusArguments())];
    }

    /**
     * Retrieve country configuration
     *
     * @return mixed[]
     */
    private function getCountryId()
    {
        return ['arguments' => $this->getArguments($this->getCountryIdArguments())];
    }

    /**
     * Retrieve status arguments
     *
     * @return mixed[]
     */
    private function getStatusArguments()
    {
        return [
            'prefer' => 'toggle',
            'valueMap' => ['false' => '0', 'true' => '1']
        ];
    }

    /**
     * Retrieve country arguments
     *
     * @return mixed[]
     */
    private function getCountryIdArguments()
    {
        return [
            'options' => $this->countrySource->toOptionArray()
        ];
    }

    /**
     * Retrieve arguments data
     *
     * @param mixed[] $config
     * @return mixed[]
     */
    private function getArguments(array $config)
    {
        return ['data' => ['config' => $config]];
    }
}
