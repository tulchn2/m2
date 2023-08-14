<?php

namespace Ecommage\DiscountReports\Block\Adminhtml\View;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogRule\Model\Indexer\ProductPriceCalculator;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Directory\Model\Currency;
use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Template
{
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

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
    private $rulePrice = 0;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ProductPriceCalculator $productPriceCalculator,
        Currency $currency,
        StoreManager $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productPriceCalculator = $productPriceCalculator;
        $this->currency = $currency;
        $this->storeManager = $storeManager;
    }

    public function getCatalogruleProduct($productId)
    {
        $rules = json_decode($this->getCatalogrule(), true);
        return $rules[$productId];
    }

    public function getSalesruleProduct($productId)
    {
        $rules =  json_decode($this->getSalesrule(), true);
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

    public function setrulePrice($price)
    {
        $this->rulePrice = $price;
    }
    public function getRulePrice()
    {
        return $this->rulePrice;
    }

    public function fromatPrice($price)
    {
        $storeId = $this->getStoreId() ?? Store::DEFAULT_STORE_ID;
        $store = $this->storeManager->getStore($storeId);
        $currencyCode = $store->getBaseCurrency()->getCurrencyCode();

        $basePurchaseCurrency = $this->currency->load($currencyCode);
        return $basePurchaseCurrency->format($price, [], false);
    }

    public function getShippingData()
    {
        return json_decode($this->getShipping(), true);
    }
}
