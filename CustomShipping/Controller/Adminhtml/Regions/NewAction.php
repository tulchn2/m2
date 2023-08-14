<?php

namespace Ecommage\CustomShipping\Controller\Adminhtml\Regions;

class NewAction extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_forward('edit');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ecommage_CustomShipping::regions');
    }
}
