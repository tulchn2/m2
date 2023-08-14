<?php

namespace Ecommage\Validation\Observer;

use Ecommage\Blog\Model\BlogFactory;
use Ecommage\Blog\Helper\Data;
class AutoCreateABlog implements \Magento\Framework\Event\ObserverInterface
{
    protected $_postFactory;
    protected $_helperData;
    public function __construct(
         BlogFactory $postFactory,
         Data $helperData
    ) {
         $this->_postFactory = $postFactory;
         $this->_helperData = $helperData;
    }
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
        $customer = $observer->getData('customer');
        if($idCustomer = $customer->getId()){
            $post = $this->_postFactory->create();
            $PostData = [
                'title' => 'Hello '. $customer->getEmail(),
                'url_key' => 'first-post-'.time(),
                'author_id' => $idCustomer,
                'status' => $this->_helperData->getPublishedVal(),
                'content' => 'Hello '. $customer->getEmail(),
            ];
            $post->setData($PostData)->save();
        }
	}
}