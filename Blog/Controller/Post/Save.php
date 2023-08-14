<?php
namespace Ecommage\Blog\Controller\Post;

use Magento\Framework\Controller\Result\JsonFactory;
use Ecommage\Blog\Model\BlogFactory;
use Ecommage\Blog\Helper\Data;
use Magento\Framework\Controller\Result\RedirectFactory;

class Save extends \Magento\Framework\App\Action\Action
{
     protected $_postFactory;
     protected $_helperData;
     public $_resultJsonFactory;
     public $_resultRedirectFactory;


     public function __construct(
          \Magento\Framework\App\Action\Context $context,
          BlogFactory $postFactory,
          Data $helperData,
          JsonFactory $resultJsonFactory,
          RedirectFactory $resultRedirectFactory
     ) {
          $this->_postFactory = $postFactory;
          $this->_helperData = $helperData;
          $this->_resultJsonFactory = $resultJsonFactory;
          $this->_resultRedirectFactory = $resultRedirectFactory;
          return parent::__construct($context);
     }
     public function execute()
     {
          $input = $this->getRequest()->getPostValue();
          if (isset($input['editRecordId'])) {
               $resultPage = $this->_resultJsonFactory->create();
               $resultPage->setData($input);
          } else {
               $resultPage = $this->_resultRedirectFactory->create();
               $resultPage->setPath('*/*/index');
          }
          if ($this->getRequest()->isPost()) {
               try {
                    if(isset($input['title'])){
                         $post = $this->_postFactory->create();
                         if (isset($input['editRecordId'])) {
                              $post->load($input['editRecordId']);
                              $post->addData($input);
                              $post->setId($input['editRecordId']);
                              $post->save();
                              $this->messageManager->addSuccessMessage(__('Post has been updated successfully.'));
                              $valPublished = (int)$this->_helperData->getPublishedVal();
                              // $input['val_published'] = (int)$input['status'] !== $valPublished ? $valPublished : 0;
                              $resultPage->setData(['val_published' => (int)$input['status'] !== $valPublished ? $valPublished : 0]);
                              $resultPage->setData($input);
                         } else {
                              $input['author_id'] = $this->_helperData->getCustomerId();
                              $post->setData($input)->save();
                              $this->messageManager->addSuccessMessage(__('Post has been created successfully.'));
                         }
                    }
               } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__($e->getMessage()));
                    $resultPage->setData(['error_message' => $e->getMessage()]);
               }
          }
          return $resultPage;
     }
}
?>