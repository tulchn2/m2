<?php

namespace Ecommage\Banners\Controller\Adminhtml\Banners;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\LayoutFactory;

class Chooser extends Action
{
    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var RawFactory
     */
    private $rawResultFactory;

    public function __construct(
        Context $context,
        LayoutFactory $layoutFactory,
        RawFactory $rawResultFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->rawResultFactory = $rawResultFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $uniqId = (string)$this->getRequest()->getParam('uniq_id');
        $layout = $this->layoutFactory->create();
        $chooser = $layout->createBlock(
            \Ecommage\Banners\Block\Adminhtml\Widget\Chooser::class,
            '',
            ['data' => ['id' => $uniqId]]
        );
        $rawResult = $this->rawResultFactory->create();

        return $rawResult->setContents($chooser->toHtml());
    }
}
