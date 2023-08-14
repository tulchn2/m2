<?php

namespace Ecommage\Banners\Controller\Adminhtml\Banners;

class NewAction extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_forward('edit');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ecommage_banners::banners');
    }
}
