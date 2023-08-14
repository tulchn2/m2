<?php
namespace Ecommage\CustomerReview\Model\ResourceModel\Reviews;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_eventPrefix = 'ecommage_reviews';
    protected $_eventObject = 'review_collection';
    protected function _construct()
    {
        $this->_init(
            \Ecommage\CustomerReview\Model\Reviews::class,
            \Ecommage\CustomerReview\Model\ResourceModel\Reviews::class
        );
    }
}
