<?php
namespace Ecommage\Blog\Controller\Post;

use Ecommage\Blog\Helper\Data;
class Delete extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_postFactory;

    protected $helpData;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Ecommage\Blog\Model\BlogFactory $postFactory,
        Data $helpData
    ) {
        $this->_postFactory = $postFactory;
        $this->helpData = $helpData;
        return parent::__construct($context);
    }

    public function execute()
    {
        try{
            $id = $this->getRequest()->getParam('id');
            $post = $this->_postFactory->create();
            $post = $post->load($id);
            if((int)$post->getData('author_id') == (int)$this->helpData->getCustomerId()){
                $post->delete();
                $this->messageManager->addSuccessMessage(__("Post '%1' has been deleted successfully.", $post->getData('title')));
            }else{
                $this->messageManager->addWarningMessage(__("We can't delete the post right now."));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("We can't delete the post right now."));
        }
        return $this->_redirect('*/*/index');
    }
}
?>