<?php

namespace Ecommage\RecentlyViewed\Block\Product;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class RecentlyViewed extends AbstractProduct
{
    /**
     * Default value for products page size
     */
    const DEFAULT_PRODUCTS_PAGE_SIZE = 9;
    /**
     * Catalog product visibility
     *
     * @var Visibility
     */
    protected $catalogProductVisibility;
    /**
     * Product collection factory
     *
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * RecentlyProduct constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param CollectionFactory                      $productCollectionFactory
     * @param Visibility                             $catalogProductVisibility
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @return array|Collection
     */

    public function createCollection()
    {
        $productIds = $this->getData('product_ids');
        if (!$productIds) {
            return [];
        }
        $collection = $this->productCollectionFactory->create()->addIdFilter($productIds);
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
                           ->addStoreFilter()
                           ->setPageSize(self::DEFAULT_PRODUCTS_PAGE_SIZE);
        $collection->getSelect()
        ->order(new \Zend_Db_Expr('FIELD(e.entity_id, ' . implode(',', $productIds).')'));
        $collection->getSelect()->distinct(true);

        return $collection;
    }
}
