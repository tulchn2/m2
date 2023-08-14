<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\RecentlyViewed\Controller\Viewed;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class Index extends Action
{

    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Index constructor.
     *
     * @param Context     $context
     * @param JsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $result = $this->_resultJsonFactory->create();
        $block = $resultPage->getLayout()
            ->createBlock(
                \Ecommage\RecentlyViewed\Block\Product\RecentlyViewed::class,
                'ecommage_recently_viewed_product',
                [
                    'data' => [
                        'product_ids' => $this->getProductIds(),
                        'show_buttons' => 'add_to_cart',
                        'title' => __('Recently Viewed Products')
                    ]
                ]
            )
            ->setTemplate('NiceForNow_RecentlyProduct::product/recently_view_modal_product.phtml')
            ->toHtml();
        $result = $this->_resultJsonFactory->create();
        $result->setData($block);
        return $result;
    }

    /**
     * Convert Products Ids from Local Storage to Array
     *
     * @return array
     */
    public function getProductIds()
    {
        $productIds = [];
        $localStorageData = $this->getRequest()->getParam('recently_viewed_product');
        $arrRrecentlyViewedProduct = json_decode($localStorageData, true);
        if ($arrRrecentlyViewedProduct && count($arrRrecentlyViewedProduct)) {
            $added_at = array_column($arrRrecentlyViewedProduct, 'added_at');
            array_multisort($added_at, SORT_DESC, $arrRrecentlyViewedProduct);
            $productIds = array_column($arrRrecentlyViewedProduct, 'product_id');
        }
        return $productIds;
    }
}
