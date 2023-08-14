<?php

namespace Ecommage\CustomShipping\Model;

use Magento\Framework\Registry;
use Ecommage\CustomShipping\Model\ResourceModel\Regions\CollectionFactory;
use Ecommage\CustomShipping\Model\ResourceModel\RegionsFrom\CollectionFactory as RegionFromCollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $loadedData;
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Region From collection factory
     *
     * @var RegionFromCollectionFactory
     */
    private $regionFromCollectionFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $regionsCollectionFactory,
        Registry $coreRegistry,
        RegionFromCollectionFactory $regionFromCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $regionsCollectionFactory->create();
        $this->coreRegistry = $coreRegistry;
        $this->regionFromCollectionFactory = $regionFromCollectionFactory;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $currentRegionCost = $this->coreRegistry->registry('region_cost');
        $data = $currentRegionCost->getData();
        if (!empty($data)) {
            $region = $this->collection->getNewEmptyItem();
            $region->setData($data);
            $region['edit'] = true;
            $regionFrom = $this->regionFromCollectionFactory->create()
            ->addFieldToFilter('region_id', $region->getId());
            if ($regionFrom->getSize()) {
                $region->setData('advanced_costs', $regionFrom->getData());
                $region->setData('region_id_from_list', array_column($regionFrom->getData(), 'region_id_from'));
            }

            $this->loadedData[$region->getId()] = $region->getData();
        }
        return $this->loadedData;
    }
}
