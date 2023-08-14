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

class CartRuleDiscount extends Column
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
        $arrayDiscount = [];
        $arrayLabel = [];
        if ($productRules = json_decode($item[preg_replace('/_show$/', '', $this->getData('name'))], true)) {
            foreach ($productRules as $rules) {
                foreach ($rules as $rule) {
                    if (isset($rule['rule_id'])) {
                        $key = $rule['rule_id'];
                        if (!array_key_exists($key, $arrayDiscount)) {
                            $arrayDiscount[$key] = $rule['discount'];
                            $arrayLabel[$key] = $rule['rule_name'];
                        } else {
                            $arrayDiscount[$key] += $rule['discount'];
                        }
                    }
                }
            }
            foreach ($arrayDiscount as $key => $_item) {
                $content .='-&nbsp;' . $arrayLabel[$key];
                $content .= ":&nbsp;&nbsp;-". $this->fromatPrice($_item, $storeId) . "<br/>";
            }
            $content .= '- Total Discount:&nbsp;&nbsp;-';
            $content .= $this->fromatPrice(array_sum($arrayDiscount), $storeId) . "<br/>";
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
