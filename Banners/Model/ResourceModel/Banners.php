<?php
namespace Ecommage\Banners\Model\ResourceModel;

class Banners extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('ecommage_banner', 'banner_id');
    }
}
