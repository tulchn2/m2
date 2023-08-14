<?php

namespace Ecommage\Blog\Controller\Adminhtml\Post;

class Index extends \Magento\Backend\App\Action
{
	protected $resultPageFactory = false;

	/**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
	const ADMIN_RESOURCE = 'Ecommage_Blog::posts';

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}

	public function execute()
	{
		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->prepend((__('Posts')));

		return $resultPage;
	}

}