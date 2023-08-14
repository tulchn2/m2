<?php
namespace Ecommage\Blog\Controller\Adminhtml\Post;
use Magento\Backend\App\Action;

class Delete extends Action
{

    private $postFactory;
    const ADMIN_RESOURCE = 'Ecommage_Blog::posts';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ecommage\Blog\Model\BlogFactory $postFactory
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
            return $resultRedirect->setPath('*/*/index', array('_current' => true));
        }
        try{
            $post->delete();
            $this->messageManager->addSuccess(__('Your post has been deleted !'));
        } catch (Exception $e) {
            $this->messageManager->addError(__('Error while trying to delete post: '));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index', array('_current' => true));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index', array('_current' => true));
    }
}