<?php
namespace Ecommage\Attributes\Block;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Helper\Product as ImageHelper;

class Slider extends \Magento\Framework\View\Element\Template
{
    protected $_productRepository;
    protected $productStatus;
    protected $productVisibility;
    protected $_productCollectionFactory;
    public $imageHelper;
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        ProductRepository $productRepository,
        CollectionFactory $productCollectionFactory,
        Status $productStatus,
        Visibility $productVisibility,
        ImageHelper $imageHelper,
        )
	{
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productRepository = $productRepository;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->imageHelper = $imageHelper;
		parent::__construct($context);
	}
    public function getSliderData()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addFieldToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        $collection->addAttributeToSelect(
                ['name', 'image']
            )
            ->addAttributeToFilter(
                [
                    ['attribute' => 'short_description', 'notnull' => true],
                    ['attribute' => 'ecommage', 'is' => new \Zend_Db_Expr('NOT NULL')],
                ],
            )
            ->setPageSize(10)->setCurPage(1);
            $storeId = (int)$this->getRequest()->getParam('store', 0);
            if ($storeId > 0) {
                $collection->addStoreFilter($storeId);
            }
        return $collection;
    }
    public function getMainImageUrl(\Magento\Catalog\Model\Product $product)
    {
        return $this->imageHelper->getImageUrl($product);
    }
}