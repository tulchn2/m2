<?php
namespace Ecommage\DiscountReports\Model\ResourceModel\Reports;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_eventPrefix = 'ecommage_discount_reports';
    protected $_eventObject = 'reports_collection';
    protected function _construct()
    {
        $this->_init(
            \Ecommage\DiscountReports\Model\Reports::class,
            \Ecommage\DiscountReports\Model\ResourceModel\Reports::class
        );
    }
}
