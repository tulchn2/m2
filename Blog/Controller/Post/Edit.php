<?php
namespace Ecommage\Blog\Controller\Post;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Magento\Customer\Model\Session;
class Edit extends \Magento\Framework\App\Action\Action
{
     protected $_resultPageFactory;
     protected $_request;
     protected $_coreRegistry;

     protected $_customerSession;

     public function __construct(
          \Magento\Framework\App\Action\Context $context,
          \Magento\Framework\View\Result\PageFactory $pageFactory,
          Http $request,
          Registry $coreRegistry,
          Session $customerSession
     ) {
          $this->_customerSession = $customerSession;
          $this->_customerSession->authenticate();
          $this->_resultPageFactory = $pageFactory;
          $this->_request = $request;
          $this->_coreRegistry = $coreRegistry;
          return parent::__construct($context);
     }

     public function execute()
     {
          $resultPage = $this->_resultPageFactory->create();
          $id = $this->_request->getParam('id');
          $titlePage = $id ? __('Edit Post Information') : __('New Post');
          $resultPage->getConfig()->getTitle()->set($titlePage);
          $block = $resultPage->getLayout()->getBlock('ecommage_blog_index_edit');
          if ($block) {
               $block->setRefererUrl($this->_redirect->getRefererUrl());
          }
          $this->_coreRegistry->register('editRecordId', $id);
          return $resultPage;
     }
}