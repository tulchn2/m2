<?php

namespace Ecommage\CustomShipping\Model\ResourceModel;

class Regions extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('directory_country_region_cost', 'region_id');
        $this->_isPkAutoIncrement = false;
    }
}
