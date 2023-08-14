<?php

namespace Ecommage\Banners\Controller\Adminhtml\Banners;

use Magento\Backend\App\Action;

class Delete extends Action
{

    private $postFactory;
    const ADMIN_RESOURCE = 'Ecommage_Banners::posts';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ecommage\Banners\Model\BannersFactory $postFactory
    ) {
        parent::__construct($context);
        $this->postFactory = $postFactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $rowData = $this->postFactory->create();

        if (!($post = $rowData->load($id))) {
            $this->messageManager->addError(__('Unable to proceed. Please, try again.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index', ['_current' => true]);
        }
        try {
            $post->delete();
            $this->messageManager->addSuccess(__('Your banners has been deleted !'));
        } catch (Exception $e) {
            $this->messageManager->addError(__('Error while trying to delete banners: '));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index', ['_current' => true]);
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }
}
