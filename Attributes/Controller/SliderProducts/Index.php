<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\Attributes\Controller\SliderProducts;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_scopeConfig;
	protected $_resultPageFactory;
	protected $_customerSession;

	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
	) {
		parent::__construct($context);
		$this->_resultPageFactory = $resultPageFactory;
	}

	public function execute()
	{
		$resultPage = $this->_resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->set(__('Product Slider'));

		return $resultPage;
	}	
}