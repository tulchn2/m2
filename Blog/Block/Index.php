<?php
namespace Ecommage\Blog\Block;

use Magento\Framework\View\Element\Template\Context;
use Ecommage\Blog\Helper\Data;
use Ecommage\Blog\Model\ResourceModel\Blog\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
class Index extends \Magento\Framework\View\Element\Template
{
	protected $_blogFactory;
	protected $helpData;
	protected $blogCollectionFactory;
	protected $_storeManager;

	public function __construct(
		Context $context,
		CollectionFactory $blogCollectionFactory,
		StoreManagerInterface $storeManager,
		Data $helpData)
	{
		$this->_storeManager = $storeManager;
		$this->helpData = $helpData;
		$this->blogCollectionFactory = $blogCollectionFactory->create();
		parent::__construct($context);
	}

	public function getBlogCollection(){
		$post = $this->blogCollectionFactory;
		$post->addFieldToFilter('author_id', $this->helpData->getCustomerId());
		return $post;
	}

	public function getBlogPublishedCollection(){
		$post = $this->blogCollectionFactory;
		$post->addFieldToFilter('status', $this->helpData->getPublishedVal());
		return $post;
	}

	public function getStoreManagerData(){
		echo $this->_storeManager->getStore()->getId() . '<br />';
        
        // by default: URL_TYPE_LINK is returned
        echo $this->_storeManager->getStore()->getBaseUrl() . '<br />';        
        
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . '<br />';
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_DIRECT_LINK) . '<br />';
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . '<br />';
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC) . '<br />';
        
        echo $this->_storeManager->getStore()->getUrl('product/33') . '<br />';
        
        echo $this->_storeManager->getStore()->getCurrentUrl(false) . '<br />';
            
        echo $this->_storeManager->getStore()->getBaseMediaDir() . '<br />';
            
        echo $this->_storeManager->getStore()->getBaseStaticDir() . '<br />';   
	}
	

	public function getStatusName($status = ''){
		$name = '';
		$arrOption = $this->helpData->getStatusOptions();
		if($arrOption && is_array($arrOption) && isset($arrOption[$status])) {
			$name = $arrOption[$status];
		}
		return $name;
	}
	
}