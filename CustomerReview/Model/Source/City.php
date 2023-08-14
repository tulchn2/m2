<?php

namespace Ecommage\CustomerReview\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;

class City implements OptionSourceInterface
{
    const COUNTRY_ID = 'VN';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var array
     */
    private $options;

    /**
     * City constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->collectionFactory->create()->addCountryFilter(self::COUNTRY_ID);
        return $collection->toOptionArray();
    }

    /**
     * Filter name by its code or name
     *
     * @param string|array $region
     * @return string
     */
    public function getDefaultName($region)
    {
        $collection = $this->collectionFactory->create()
        ->addCountryFilter(self::COUNTRY_ID)
        ->addFieldToFilter('main_table.region_id', ['eq' => $region])->getFirstItem();
        if ($collection) {
            return $collection->getDefaultName();
        }
        return '';
    }
}
