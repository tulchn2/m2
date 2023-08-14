<?php

namespace Ecommage\DiscountReports\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Directory\Model\Currency;
use Magento\Store\Model\Store;

class Discount extends Column
{
    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * System store
     *
     * @var SystemStore
     */
    protected $systemStore;

    /**
     * Store manager
     *
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $storeKey;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SystemStore $systemStore
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     * @param string $storeKey
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SystemStore $systemStore,
        Escaper $escaper,
        Currency $currency,
        StoreManager $storeManager,
        array $components = [],
        array $data = [],
        $storeKey = 'store_id'
    ) {
        $this->systemStore = $systemStore;
        $this->escaper = $escaper;
        $this->currency = $currency;
        $this->storeManager = $storeManager;

        $this->storeKey = $storeKey;
        parent::__construct($context, $uiComponentFactory, $components, $data);
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
        $content = '';
        $storeId = $item['store_id'];
        $totalDiscount = 0;
        $totalOriginalPrice = 0;
        $totalDiscountPrice = 0;
        if ($productRules = json_decode($item[preg_replace('/_show$/', '', $this->getData('name'))], true)) {
            foreach ($productRules as $rule) {
                $totalOriginalPrice += $rule[0]['original_price'] * $rule[0]['qty_ordered'];
                $totalDiscountPrice += $rule[0]['discount_calculation_price'] * $rule[0]['qty_ordered'];
                $totalDiscount -= $totalOriginalPrice - $totalDiscountPrice;
            }
            $content .= '- Total Original Price:&nbsp;&nbsp;';
            $content .= $this->fromatPrice($totalOriginalPrice, $storeId) . "<br/>";
            $content .= '- Total Discount Price:&nbsp;&nbsp;';
            $content .= $this->fromatPrice($totalDiscountPrice, $storeId) . "<br/>";
            $content .= '- Total Discount:&nbsp;&nbsp;' . $this->fromatPrice($totalDiscount, $storeId) . "<br/>";
        }
        return $content;
    }

    protected function fromatPrice($price, $storeId)
    {
        $storeId = (int) $storeId !== 0 ? $storeId :
                        $this->context->getFilterParam('store_id', Store::DEFAULT_STORE_ID);
        $store = $this->storeManager->getStore($storeId);
        $currencyCode = $store->getBaseCurrency()->getCurrencyCode();

        $basePurchaseCurrency = $this->currency->load($currencyCode);
        return $basePurchaseCurrency->format($price, [], false);
    }
}
