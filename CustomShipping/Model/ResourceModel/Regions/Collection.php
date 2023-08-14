<?php
namespace Ecommage\CustomShipping\Model\ResourceModel\Regions;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_eventPrefix = 'ecommage_regions_cost';
    protected $_eventObject = 'regions_collection';
    protected $_idFieldName = 'main_table.region_id';
    protected function _construct()
    {
        $this->_init(
            \Ecommage\CustomShipping\Model\Regions::class,
            \Ecommage\CustomShipping\Model\ResourceModel\Regions::class
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['table_from' => $this->getTable('from_country_region_cost')],
            'main_table.region_id = table_from.region_id',
            ['cost_from' => 'GROUP_CONCAT(table_from.region_id_from, ":", table_from.cost)']
        )->group('region_id');
    }
    
}
