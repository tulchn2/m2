<?php
namespace Ecommage\Blog\Controller\Post;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;

class Insert extends \Magento\Framework\App\Action\Action
{
	protected $_resultForwardFactory;
	protected $_customerSession;
	public function __construct(
		Context $context,
		ForwardFactory $resultForwardFactory
	) {
		$this->_resultForwardFactory = $resultForwardFactory;
		return parent::__construct($context);
	}
	public function execute()
	{
		$resultPage = $this->_resultForwardFactory->create();
		return $resultPage->forward('edit');;
	}

}