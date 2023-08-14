<?php

namespace Ecommage\Blog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Ecommage\Blog\Model\Source\Post\Status;

class Data extends AbstractHelper
{
    protected $registry;
    protected $customerSession;

    protected $statusOptions;


    public function __construct(
        Session $customerSession,
        Registry $registry,
        Status $statusOptions
    ) {
        $this->registry = $registry;
        $this->customerSession = $customerSession;
        $this->statusOptions = $statusOptions;
    }
    
    public function getCustomerId(){
        return $this->customerSession->getCustomerId();
    }
    public function isLoggedIn(){
        return $this->customerSession->isLoggedIn();
    }
    public function getStatusOptions(){
        $arr = $this->statusOptions->getOptions();
        return $arr;
    }
    
    public function getPublishedVal(){
        return $this->statusOptions::PUBLICATION;
    }
}