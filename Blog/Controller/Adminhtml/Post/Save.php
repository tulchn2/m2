<?php

namespace Ecommage\Blog\Controller\Adminhtml\Post;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Ecommage\Blog\Model\BlogFactory
     */
    var $blogFactory;

    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ecommage\Blog\Model\BlogFactory $blogFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ecommage\Blog\Model\BlogFactory $blogFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context);
        $this->blogFactory = $blogFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getParam('blog_post');
        if (!$data) {
            $this->_redirect('ecommage/post/edit');
            return;
        }
        try {
            $rowData = $this->blogFactory->create();
            unset($data['updated_at']);
            $rowData->setData($data)->save();
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('ecommage/post/index');
    }
}