<?php
namespace Ecommage\CustomerReview\Model\ResourceModel;

class Reviews extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('ecommage_reviews', 'review_id');
    }
}
