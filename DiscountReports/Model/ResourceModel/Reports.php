<?php
namespace Ecommage\DiscountReports\Model\ResourceModel;

class Reports extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('discount_reports', 'report_id');
    }
}
