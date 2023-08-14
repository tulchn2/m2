<?php
namespace Ecommage\HotDealWidget\Block\Widget\Product;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Data\Helper\PostHelper;

/**
 * Catalog Products List widget block
 *
 */
class HotDeal extends AbstractProduct implements BlockInterface
{

    /**
     * Default template
     */
    protected $_template = "widget/products.phtml";

    /**
     * @var Visibility
     */
    protected $catalogProductVisibility;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var PostHelper
     */
    protected $postHelper;

    /**
     * NewWidget constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\Data\Helper\PostHelper $postHelper
     *
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        PostHelper $postHelper,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->postHelper = $postHelper;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @inheritdoc
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->createCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Prepare and return product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     * @throws LocalizedException
     */
    public function createCollection()
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $productIds = $this->getProductsIdChooser();
        $collection = $this->productCollectionFactory->create()->addIdFilter($productIds);
        if ($this->getData('store_id') !== null) {
            $collection->setStoreId($this->getData('store_id'));
        }
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
        ->addStoreFilter()->addAttributeToFilter(
            'special_from_date',
            ['date' => true, 'to' => $todayEndOfDayDate]
        )->addAttributeToFilter(
            'special_to_date',
            [
                'or' => [
                    0 => [
                        'date' => true,
                        'from' => $todayStartOfDayDate
                    ],
                    1 => [ 'null' => true ]
                ]
            ]
        );
        return $collection;
    }

    /**
     * Get pruduct ids in the config
     *
     * @return array
     */
    protected function getProductsIdChooser()
    {
        $strId = $this->getData('product_ids_choose');
        $arrId = [];
        $ids = explode('}{', $strId);
        foreach ($ids as $id) {
            $id = str_replace(['{', '}'], '', $id);
            $arrId[] = $id;
        };
        return $arrId;
    }

    /**
     * Convert Discount Percentage
     *
     * @return int
     */

    public function getDiscountPercentage($product)
    {
        $specialprice = $product->getSpecialPrice();
        $price = $product->getPrice();
        if ($price && $specialprice) {
            return round((($price-$specialprice)/$price)*100);
        }
        return 0;
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

    /**
     * get data for post by javascript in format acceptable to $.mage.dataPost widget
     *
     * @param string $url
     * @param array $data
     * @return string
     */
    public function getPostData($url, array $data = [])
    {
        return $this->postHelper->getPostData($url, $data);
    }

    /**
     * get data for post by javascript in format acceptable to $.mage.dataPost widget
     *
     * @return string
     */
    public function getDateFrom()
    {
        return $this->getData('date_from');
    }
    public function getDateTo()
    {
        return $this->getData('date_to');
    }
}
