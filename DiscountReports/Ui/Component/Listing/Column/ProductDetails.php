<?php

namespace Ecommage\DiscountReports\Ui\Component\Listing\Column;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\Store;
use Magento\Framework\View\LayoutFactory;

class ProductDetails extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    private $store_id = Store::DEFAULT_STORE_ID;
    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param array $components = []
     * @param array $data = []
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        CollectionFactory $productCollectionFactory,
        LayoutFactory $layoutFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $name = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$name] = $this->prepareItem($item);
            }
        }
        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $this->setStoreId($item['store_id']);
        $productCollection = $this->getProducts($item['product_ids']);
        $content = '';
        $block = $this->layoutFactory->create()->createBlock(
            \Ecommage\DiscountReports\Block\Adminhtml\View\Grid::class,
            'product.detail',
            ['data' => [
                'products' => $productCollection,
                'store_id' => $this->getStoreId(),
                'catalogrule' => $item['catalogrule_product'],
                'salesrule' => $item['salesrule'],
                'shipping' => $item['shipping']
                ]
            ]
        );
        if ($block) {
            $block->setTemplate('Ecommage_DiscountReports::view/grid.phtml');
            return $block->toHtml();
        }
        return $content;
    }
    public function getProducts($productIds)
    {
        $productIds = explode(',', $productIds);
        $collection = $this->productCollectionFactory->create()->addIdFilter($productIds);
        $collection->addAttributeToSelect(['name']);
        return $collection;
    }
    public function getStoreId()
    {
        return $this->store_id;
    }
    
    public function setStoreId($storeId)
    {
        $this->store_id = $storeId;
    }
}
