<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ecommage\CustomShipping\Ui\Component\Form\Regions;

use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Registry;
use Ecommage\CustomShipping\Model\ResourceModel\Regions\CollectionFactory as RegionsCostCollectionFactory;

/**
 * Provide option values for UI
 *
 * @api
 */
class Options implements OptionSourceInterface
{
    /**
     * Region cost collection factory
     *
     * @var RegionsCostCollectionFactory
     */
    private $regionCostCollectionFactory;

    /**
     * Region collection factory
     *
     * @var CollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * Source data
     *
     * @var null|array
     */
    private $sourceData;
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param CollectionFactory $regionCollectionFactory
     * @param Registry $coreRegistry
     * @param RegionsCostCollectionFactory $regionCostCollectionFactory
     *
     */
    public function __construct(
        CollectionFactory $regionCollectionFactory,
        Registry $coreRegistry,
        RegionsCostCollectionFactory $regionCostCollectionFactory
    ) {
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->regionCostCollectionFactory = $regionCostCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = [];
        if (null === $this->sourceData) {
            $regionsCostIds = $this->regionCostCollectionFactory->create()->getAllIds();
            $currentRegionCost = $this->coreRegistry->registry('region_cost');
            $currentId = $currentRegionCost->getRegionId();
            if ($currentId) {
                if ($currentId && (($key = array_search($currentId, $regionsCostIds)) !== false)) {
                    unset($regionsCostIds[$key]);
                }
            }
            $regionCollection = $this->regionCollectionFactory->create()
            ->addFieldToFilter('main_table.region_id', ['nin' => $regionsCostIds]);

            $propertyMap = [
                'value' => 'region_id',
                'title' => 'default_name',
                'code' => 'code',
                'country_id' => 'country_id',

            ];
            foreach ($regionCollection as $item) {
                $option = [];
                foreach ($propertyMap as $code => $field) {
                    $option[$code] = $item->getData($field);
                }
                $option['label'] = $item->getName();
                $options[] = $option;
            }
            
            if (count($options) > 0) {
                array_unshift(
                    $options,
                    ['title' => '', 'value' => '', 'label' => __('Please select a region, state or province.')]
                );
            }
        }
        $this->sourceData = $options;
        return $this->sourceData;
    }
}
