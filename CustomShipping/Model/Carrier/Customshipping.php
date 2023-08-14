<?php

namespace Ecommage\CustomShipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Ecommage\CustomShipping\Model\ResourceModel\Regions\CollectionFactory as RegionsCostCollectionFactory;

/**
 * Custom shipping model
 */
class Customshipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'customshipping';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    private $rateMethodFactory;

    /**
     * Region cost collection factory
     *
     * @var RegionsCostCollectionFactory
     */
    private $regionCostCollectionFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Ecommage\CustomShipping\Model\ResourceModel\Regions\CollectionFactory $regionCostCollectionFactory
     *
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        RegionsCostCollectionFactory $regionCostCollectionFactory,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->regionCostCollectionFactory = $regionCostCollectionFactory;
    }

    /**
     * Custom Shipping Rates Collector
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $shippingCost = (float)$this->getConfigData('shipping_cost');

        if ($request->getDestCountryId() == 'VN') {
            $regionsCostCollection = $this->regionCostCollectionFactory->create()
                ->addFieldToFilter('country_id', $request->getDestCountryId())
                ->addFieldToFilter('main_table.region_id', $request->getDestRegionId())
                ->getFirstItem();
            if (!empty($regionsCostCollection->getCost())) {
                $shippingCost = (float) $regionsCostCollection->getCost();
            }
            if (!empty($regionsCostCollection->getCostFrom())) {
                $arrCost = explode(",", $regionsCostCollection->getCostFrom());
                foreach ($arrCost as $regionAndCost) {
                    $regionCost = explode(":", $regionAndCost);
                    if ($request->getRegionId() == $regionCost[0]) {
                        $shippingCost = (float) $regionCost[1];
                        break;
                    }
                }
            }
        }

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $method->setPrice($shippingCost);
        $method->setCost($shippingCost);

        $result->append($method);

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
