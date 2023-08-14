<?php

namespace Ecommage\CustomShipping\Model\ResourceModel;

class RegionsFrom extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('from_country_region_cost', 'value_id');
    }
}
