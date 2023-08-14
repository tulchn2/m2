<?php

namespace Ecommage\HotDealWidget\Controller\Adminhtml\Product\Widget;

use Magento\Framework\Escaper;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;

class Chooser extends \Magento\Catalog\Controller\Adminhtml\Product\Widget\Chooser
{

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory,
        Escaper $escaper
    ) {
        parent::__construct($context, $resultRawFactory, $layoutFactory);
        $this->escaper = $escaper;
    }

    /**
     * Chooser Source action.
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $massAction = $this->getRequest()->getParam('use_massaction', false);
        $productTypeId = $this->getRequest()->getParam('product_type_id', null);

        $layout = $this->layoutFactory->create();
        $productsGrid = $layout->createBlock(
            \Ecommage\HotDealWidget\Block\Adminhtml\Product\Widget\MultiChooser::class,
            '',
            [
                'data' => [
                    'id' => $this->escaper->escapeHtml($uniqId),
                    'use_massaction' => $massAction,
                    'product_type_id' => $productTypeId,
                    'category_id' => (int)$this->getRequest()->getParam('category_id'),
                ],
            ]
        );

        $html = $productsGrid->toHtml();

        if (!$this->getRequest()->getParam('products_grid')) {
            $categoriesTree = $layout->createBlock(
                \Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser::class,
                '',
                [
                    'data' => [
                        'id' => $this->escaper->escapeHtml($uniqId) . 'Tree',
                        'node_click_listener' => $productsGrid->getCategoryClickListenerJs(),
                        'with_empty_node' => true,
                    ],
                ]
            );

            $html = $layout->createBlock(\Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser\Container::class)
                ->setTreeHtml($categoriesTree->toHtml())
                ->setGridHtml($html)
                ->toHtml();
        }

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents($html);
    }
}
