<?php
namespace Ecommage\FirstModule\Block;
use Magento\Framework\View\Element\Template\Context;
use \Psr\Log\LoggerInterface;
class Products extends \Magento\Framework\View\Element\Template
{    
  
    protected $_template = "Ecommage_FirstModule::product.phtml";
    protected $productCollectionFactory;
    protected $productVisibility;
    protected $productStatus;

    protected $_productRepository;
    protected $filterBuilder;
    protected $filterGroupBuilder;
    protected $searchCriteriaBuilder;
    protected $_productAttributeRepository;
    protected $_stockItemRepository;

    protected $_categoryCollectionFactory;
    protected $_registry;


    protected $_storeManager;
    protected $_ratingFactory;
    protected $_productFactory;
    protected $_reviewFactory;
    protected $voteCollectionFactory;
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,

        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,

        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,

        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Registry $registry,

        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewFactory,
        \Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $voteCollectionFactory,
        array $data = []
    )
    {
        $this->logger = $logger;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;

        $this->_productRepository = $productRepository;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_productAttributeRepository = $productAttributeRepository;

        $this->_stockItemRepository = $stockItemRepository;

        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_registry = $registry;


        $this->_storeManager = $storeManager;
        $this->_productFactory = $productFactory;
        $this->_ratingFactory = $ratingFactory;
        $this->_reviewFactory = $reviewFactory;
        $this->voteCollectionFactory = $voteCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getProductCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        return $collection;
    }

    public function getProductCollectionByCategories($ids)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $ids]);
        $collection->setPageSize(3);
        return $collection;
    }

    public function getProductById($id)
	{
		return $this->_productRepository->getById($id);
	}
	
	public function getProductBySku($sku)
	{
		return $this->_productRepository->get($sku);
	}
    public function getStockItem($productId)
    {
        return $this->_stockItemRepository->get($productId);
    }

    public function getCategoryCollection($isActive = true, $level = false, $sortBy = false, $pageSize = false)
    {
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');        
        
        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }
                
        // select categories of certain level
        if ($level) {
            $collection->addLevelFilter($level);
        }
        
        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }
        
        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize); 
        }    
        
        return $collection;
    }

    public function getCategoriesName($productId)
    {
        $product = $this->product->load($productId);
        $categoryIds = $product->getCategoryIds();
        $categories = $this->categoryCollection->create()->addAttributeToSelect('*')->addAttributeToFilter('entity_id', $categoryIds);
        $categoryNames = [];
        foreach ($categories as $category) {
            $categoryNames[] = $category->getName();
        }
        $categoryName = implode(',', $categoryNames);
        return $categoryName;
    }
    
    public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    }

    public function returnProdColRepoWithFitler()
    {
        $filter_1 = $this->filterBuilder
            ->setField('is_featured')
            ->setConditionType('eq')
            ->setValue(1)
            ->create();

        $filter_2 = $this->filterBuilder
            ->setField('ecommage')
            ->setConditionType('eq')
            ->setValue('1111 e12')
            ->create();

        $filter_group_1 = $this->filterGroupBuilder
            ->addFilter($filter_1)
            ->create();

        $filter_group_2 = $this->filterGroupBuilder
            ->addFilter($filter_2)
            ->create();

        $search_criteria = $this->searchCriteriaBuilder
            ->setFilterGroups([$filter_group_1, $filter_group_2])
            ->create();

        $repo = $this->_productRepository;
        $result = $repo->getList($search_criteria);
        $products = $result->getItems();
        foreach($products as $product)
        {
            echo $product->getSku() . "<br>";
        }

        return $products;
    }

     public function getReviewCollection($productId){
        $collection = $this->_reviewFactory->create()
        ->addStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->addStatusFilter(
            \Magento\Review\Model\Review::STATUS_APPROVED
        )->addEntityFilter(
            'product',
            $productId
        )->setDateOrder();
        return $collection->addRateVotes();
    }

    public function getReviews($productId)
    {
        $reviewCollection = $this->getReviewCollection($productId);
        $voteCollection = $this->voteCollectionFactory->create();

        $voteCollection->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns(['review_id', 'avg_percent' => new \Zend_Db_Expr('ROUND(SUM(percent) / COUNT(*))')])
            ->group('review_id');

        $reviewCollection->getSelect()
            ->joinLeft(
                ['vote' => $voteCollection->getSelect()],
                'rt.review_id = vote.review_id',
                ['vote.avg_percent']
            );
        return $reviewCollection;
    }

    public function getRatingCollection(){
        $ratingCollection = $this->_ratingFactory->create()
        ->getResourceCollection()
        ->addEntityFilter(
            'product' 
        )->setPositionOrder()->setStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->addRatingPerStoreName(
            $this->_storeManager->getStore()->getId()
        )->load();

        return $ratingCollection->getData();
    }
    
}