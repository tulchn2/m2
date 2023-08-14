<?php
namespace Ecommage\Blog\Controller\Adminhtml\Post;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Ecommage\Blog\Model\BlogFactory
     */
   private $postFactory;
   const ADMIN_RESOURCE = 'Ecommage_Blog::posts';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Ecommage\Blog\Model\BlogFactory $postFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Ecommage\Blog\Model\BlogFactory $postFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->postFactory = $postFactory;
    }

    /**
     * Mapped Blog List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->postFactory->create();
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getTitle();
           if (!$rowData->getId()) {
               $this->messageManager->addError(__('Post data no longer exist.'));
               $this->_redirect('ecommage/post/index');
               return;
           }
       }

       $this->coreRegistry->register('blog_post', $rowData);
       $title = $rowId ? __('Edit Post Data: ').$rowTitle : __('Add Post Data');
       $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
       $resultPage->getConfig()->getTitle()->prepend($title);
       return $resultPage;
    }
}