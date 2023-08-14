<?php
namespace Ecommage\CustomShipping\Model\ResourceModel\RegionsFrom;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_eventPrefix = 'ecommage_regions_from_cost';
    protected $_eventObject = 'regions_from_collection';
    protected function _construct()
    {
        $this->_init(
            \Ecommage\CustomShipping\Model\RegionsFrom::class,
            \Ecommage\CustomShipping\Model\ResourceModel\RegionsFrom::class
        );
    }
}
