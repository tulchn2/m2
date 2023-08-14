<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Shipment view form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Ecommage\DiscountReports\Block\Adminhtml\View;

use Magento\Framework\App\ObjectManager;
use Magento\Shipping\Helper\Data as ShippingHelper;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogRule\Model\Indexer\ProductPriceCalculator;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Directory\Model\Currency;
use Magento\Store\Model\Store;

/**
 * @api
 * @since 100.0.2
 */
class Form extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var ProductPriceCalculator
     */
    private $productPriceCalculator;
    /**
     * Store manager
     *
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * Shipping discount data storage for breakdown
     *
     * @var float
     */
    private $rulePrice = 0;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     * @param ShippingHelper|null $shippingHelper
     * @param TaxHelper|null $taxHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        OrderRepositoryInterface $orderRepository,
        CollectionFactory $productCollectionFactory,
        ProductPriceCalculator $productPriceCalculator,
        Currency $currency,
        StoreManager $storeManager,
        array $data = [],
        ?ShippingHelper $shippingHelper = null,
        ?TaxHelper $taxHelper = null
    ) {
        $this->orderRepository = $orderRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productPriceCalculator = $productPriceCalculator;
        $this->currency = $currency;
        $this->storeManager = $storeManager;

        $data['shippingHelper'] = $shippingHelper ?? ObjectManager::getInstance()->get(ShippingHelper::class);
        $data['taxHelper'] = $taxHelper ?? ObjectManager::getInstance()->get(TaxHelper::class);
        parent::__construct($context, $registry, $adminHelper, $data);
    }
    
    /**
     * Retrieve report model instance
     *
     * @return \Ecommage\DiscountReports\Model\Reports
     */
    public function getReport()
    {
        return $this->_coreRegistry->registry('discount_report');
    }

    /**
     * Retrieve invoice order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->orderRepository->get($this->getReport()->getOrderId());
    }

    public function getProducts()
    {
        $productIds = explode(',', $this->getReport()->getProductIds());
        $collection = $this->productCollectionFactory->create()->addIdFilter($productIds);
        $collection->addAttributeToSelect(['name']);
        return $collection;
    }

    /**
     * @param int $productId
     *
     * @return array;
     */
    public function getCatalogruleProduct($productId)
    {
        $rules = json_decode($this->getReport()->getCatalogruleProduct(), true);
        return $rules[$productId];
    }

    /**
     * @param int $productId
     *
     * @return array;
     */
    public function getSalesruleProduct($productId)
    {
        $rules =  json_decode($this->getReport()->getSalesrule(), true);
        return $rules[$productId];
    }

    public function productPriceCalc($rule)
    {
        $rule['default_price'] = $this->getRulePrice() ? $this->getRulePrice() : $rule['original_price'];
        $price =  $this->productPriceCalculator->calculate($rule);
        $this->setrulePrice($price);
        $total = ($rule['default_price'] - $price) * $rule['qty_ordered'];
        return $total;
    }

    public function fromatPrice($price)
    {
        $storeId = $this->getReport()->getStoreId() ?? Store::DEFAULT_STORE_ID;
        $store = $this->storeManager->getStore($storeId);
        $currencyCode = $store->getBaseCurrency()->getCurrencyCode();

        $basePurchaseCurrency = $this->currency->load($currencyCode);
        return $basePurchaseCurrency->format($price, [], false);
    }
    public function setrulePrice($price)
    {
        $this->rulePrice = $price;
    }
    public function getRulePrice()
    {
        return $this->rulePrice;
    }
    public function getShipping()
    {
        return json_decode($this->getReport()->getShipping(), true);
    }
}
