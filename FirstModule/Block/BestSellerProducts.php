<?php

namespace Ecommage\FirstModule\Block;
use Magento\Catalog\Block\Product\Context;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Reports\Block\Product\Viewed as ReportProductViewed;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory as WishlistCollectionFactory;
use Magento\Framework\Registry;
use Magento\Catalog\Model\ProductRepository;
class BestSellerProducts extends \Magento\Catalog\Block\Product\AbstractProduct
{
    const DEFAULT_PRODUCTS_COUNT = 10;
    protected $_template = "Ecommage_FirstModule::best-seller.phtml";
    protected $_bestSellersCollectionFactory;
    protected $_productCollectionFactory;
    protected $_catalogProductVisibility;
    protected $_reportProductViewed;
    protected $_wishlistCollectionFactory;
    protected $_registry;
    protected $_productRepository;
    

    public function __construct(
        Context $context,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        ReportProductViewed $reportProductViewed,
        WishlistCollectionFactory $wishlistCollectionFactory,
        Registry $registry,
        ProductRepository $productRepository,
        array $data = []
    ) {
        $this->_bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_reportProductViewed = $reportProductViewed;
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->_registry = $registry;
        $this->_productRepository = $productRepository;
        parent::__construct($context, $data);
    }
    public function getProductCollection()
    {
        $productIds = [];
        $bestSellers = $this->_bestSellersCollectionFactory->create()
            ->setPeriod(Bestsellers::AGGREGATION_MONTHLY);
            foreach ($bestSellers as $product) {
                $productIds[] = $product->getProductId();
            }
            $collection = $this->_productCollectionFactory->create()->addIdFilter($productIds);
            $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('name')
            ->addStoreFilter($this->getStoreId())->setPageSize(self::DEFAULT_PRODUCTS_COUNT);
            return $collection;
    }

    public function getFeaturedProductCollection()
    {
        $visibleProducts = $this->_catalogProductVisibility->getVisibleInCatalogIds();
        $collection = $this->_productCollectionFactory->create()->setVisibility($visibleProducts);
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect(['name', 'sku', 'is_featured'])
            ->addStoreFilter($this->getStoreId())
            ->setPageSize(self::DEFAULT_PRODUCTS_COUNT)
            ->addAttributeToFilter('is_featured', '1');
        return $collection;
    }
    public function getNewProductCollection()
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        // $colletion->setOrder('created_at','DESC'); 
        $collection = $this->_addProductAttributesAndPrices(
            $collection
        )->addStoreFilter()->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )
        ->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )
        ->addAttributeToSort(
            'news_from_date',
            'desc'
        )->setPageSize(
            self::DEFAULT_PRODUCTS_COUNT
        )->setCurPage(
            1
        );

        return $collection;
    }

    public function getOnSeltProductCollection()
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        $visibleProducts = $this->_catalogProductVisibility->getVisibleInCatalogIds();
        $collection = $this->_productCollectionFactory->create()->setVisibility($visibleProducts);
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addAttributeToFilter(
                'special_from_date',
                ['date' => true, 'to' => $todayEndOfDayDate],
                'left'
            )->addAttributeToFilter('special_to_date',
                ['or' => [0 => ['date' => true,
                'from' => $todayStartOfDayDate],
                 1 => ['is' => new \Zend_Db_Expr('null')],]],
                'left'
            )->addAttributeToSort('news_from_date','desc'
            )->addStoreFilter($this->getStoreId())->setPageSize(
                self::DEFAULT_PRODUCTS_COUNT
            );
        return $collection;
    }
    public function getReportProductCollection()
    {
        $recentViewedCollection =  $this->_reportProductViewed->getItemsCollection()->setPageSize(
            self::DEFAULT_PRODUCTS_COUNT
        )->load();
        return $recentViewedCollection;
    }

    public function getWishListProductCollection()
    {
        $collection = [];
        $wishlist = $this->_wishlistCollectionFactory->create()
            ->addCustomerIdFilter('3');
        $productIds = null;
        foreach ($wishlist as $product) {
            $productIds[] = $product->getProductId();
        }
        $collection = $this->_productCollectionFactory->create()->addIdFilter($productIds);
        $collection = $this->_addProductAttributesAndPrices($collection)->addStoreFilter($this->getStoreId())->setPageSize(self::DEFAULT_PRODUCTS_COUNT);

        return $collection;
    }
    public function getCurrentProduct()
    {        
        return  $this->getProduct();
        // return $this->_registry->registry('current_product');
    }

    public function getProductById($id)
	{
		return $this->_productRepository->getById($id);
	}

    public function getLoopFor($collection, $title ='')
	{
		if (!empty($collection)) {
            echo '<h3>'.__($title).'</h3> <br />';
            foreach ($collection as $_item) {
               echo $_item->getName(). ' :: ';
               echo $_item->getSku() . ' <br> ';
            }
         }
	}
    
}