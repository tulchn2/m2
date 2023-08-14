<?php
namespace Ecommage\Banners\Model\ResourceModel\Banners;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_eventPrefix = 'ecommage_banner';
    protected $_eventObject = 'banner_collection';
    protected function _construct()
    {
        $this->_init(\Ecommage\Banners\Model\Banners::class, \Ecommage\Banners\Model\ResourceModel\Banners::class);
    }
}
