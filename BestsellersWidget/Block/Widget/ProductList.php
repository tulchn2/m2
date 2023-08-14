<?php
namespace Ecommage\BestsellersWidget\Block\Widget;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Widget\Block\BlockInterface;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\Product\Visibility;

class ProductList extends AbstractProduct implements BlockInterface
{

    /**
     * Default template
     */
    protected $_template = "widget/product-list.phtml";
    /**
     * @var BestSellersCollectionFactory;
     */
    protected $_bestSellersCollectionFactory;
    /**
     * @var CollectionFactory;
     */
    protected $_productCollectionFactory;
    /**
     * @var CategoryCollectionFactory;
     */
    protected $_categoryCollectionFactory;
    /**
     * @var Visibility;
     */
    protected $_catalogProductVisibility;
    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER = false;

    /**
     * Default value for products per tab
     */
    const DEFAULT_PRODUCTS_PER_TAB = 8;

    /**
     * Instance of pager block
     *
     * @var \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    protected $_pager;

    /**
     * bestsellers_categories constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory  $bestSellersCollectionFactory;
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  $productCollectionFactory;
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory;
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     *
     */
    public function __construct(
        Context $context,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        CollectionFactory $productCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        Visibility $catalogProductVisibility,
        array $data = []
    ) {
        $this->_bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;

        parent::__construct($context, $data);
    }

    /**
     * Filter for selected categories
     *
     * @return $collection
     */

    public function getCategoryCollection()
    {
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'url_key']);
        $collection->addAttributeToFilter('entity_id', ['in' => $this->getCategories()]);
        return $collection;
    }
    /**
     * Filter Product Bestsellers by Categories
     *
     * @param \Magento\Catalog\Model\Category|string $category
     *
     * @return $collection
     */
    public function getProductCollectionByCategories($category)
    {
        $productIds = $this->getArrProductBestsellerId();
        $collection = $this->_productCollectionFactory->create()->addIdFilter($productIds);
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        if (is_string($category)) {
            $collection->addCategoriesFilter(['in' => $category]);
        } else {
            $collection->addCategoryFilter($category);
        }
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->setPageSize($this->getProductsCount());
        return $collection;
    }

    /**
     * return array Product Ids to filter
     *
     * @return array
     */
    public function getArrProductBestsellerId()
    {
        $productIds = [];
        $bestSellers = $this->_bestSellersCollectionFactory->create()
            ->setPeriod($this->getPeriod());
        $productIds = $bestSellers->getColumnValues('product_id');
        return $productIds;
    }

    /**
     * Return how many products should be displayed in category tab
     *
     * @return int
     */
    public function getCategories()
    {
        return $this->getData('multi_category_chooser');
    }

    /**
     * Return how many products should be displayed in category tab
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (!$this->hasData('products_count')) {
            return self::DEFAULT_PRODUCTS_PER_TAB;
        }
        return $this->getData('products_count');
    }

    /**
     * Returns the period type of the bestsellers
     *
     * @return string
     */
    public function getPeriod()
    {
        if (!$this->hasData('set_period')) {
            return Bestsellers::AGGREGATION_MONTHLY;
        }
        return $this->getData('set_period');
    }

    /**
     * Return flag whether tab 'All' need to be shown or not
     *
     * @return bool
     */
    public function showTabAll()
    {
        if (!$this->hasData('show_tab_all')) {
            $this->setData('show_tab_all', self::DEFAULT_SHOW_PAGER);
        }
        return (bool) $this->getData('show_tab_all');
    }
    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }
}
