<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\Blog\Controller\Post;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_scopeConfig;
	protected $_resultPageFactory;
	protected $_customerSession;

	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		\Magento\Customer\Model\Session $customerSession
	) {
		parent::__construct($context);
		$this->_customerSession = $customerSession;
		$this->_resultPageFactory = $resultPageFactory;
	}

	 /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_customerSession->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }
	public function execute()
	{
		$resultPage = $this->_resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->set(__('My Blogs'));

		$block = $resultPage->getLayout()->getBlock('blog_listing_index');
		if ($block) {
			$block->setRefererUrl($this->_redirect->getRefererUrl());
		}
		return $resultPage;
	}	
}