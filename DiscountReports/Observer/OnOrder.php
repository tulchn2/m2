<?php

namespace Ecommage\DiscountReports\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Ecommage\DiscountReports\Model\ReportsFactory;
use Magento\CatalogRule\Model\ResourceModel\Rule;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Utility;

class OnOrder implements ObserverInterface
{
    protected $logger;
    protected $reportsFactory;
    protected $ruleResourceModel;
    protected $catalogRuleRepository;
    protected $storeManager;
    protected $salesRuleCalcFactory;

    protected $salesRuleRepositoryInterface;

    /**
     * @var \Magento\SalesRule\Model\Utility
     */
    protected $validatorUtility;

    public function __construct(
        LoggerInterface $logger,
        ReportsFactory $reportsFactory,
        Rule $ruleResourceModel,
        StoreManagerInterface $storeManager,
        CatalogRuleRepositoryInterface $catalogRuleRepository,
        CalculatorFactory $salesRuleCalcFactory,
        RuleRepositoryInterface $salesRuleRepositoryInterface,
        Utility $validatorUtility
    ) {
        $this->reportsFactory = $reportsFactory;
        $this->logger = $logger;
        $this->ruleResourceModel = $ruleResourceModel;
        $this->storeManager = $storeManager;
        $this->catalogRuleRepository = $catalogRuleRepository;
        $this->salesRuleCalcFactory = $salesRuleCalcFactory;
        $this->validatorUtility = $validatorUtility;

        $this->salesRuleRepositoryInterface = $salesRuleRepositoryInterface;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $quote = $observer->getQuote();
        $rowData = $this->reportsFactory->create();
        $data = $order->getData();
        $data['order_id'] = $order->getId();
        $matchingKeys = preg_grep('/^shipping+/', array_keys($data));
        $data['shipping'] = json_encode(array_intersect_key($data, array_flip($matchingKeys)));
        $matchingKeysCustomer = preg_grep('/^customer+/', array_keys($data));
        $data['customer'] = json_encode(array_intersect_key($data, array_flip($matchingKeysCustomer)));
        $catalogruleProduct = [];
        $salesRuleProduct = [];
        $productIds = '';
        try {
            foreach ($quote->getItems() as $item) {
                $newItem = $item;

                $newItem->setDiscountAmount(0);
                $newItem->setBaseDiscountAmount(0);
                $salesRuleIds = explode(",", $order->getAppliedRuleIds());

                $rules = $this->ruleResourceModel->getRulesFromProduct(
                    $order->getCreatedAt(),
                    $this->storeManager->getStore($newItem->getStoreId())->getWebsiteId(),
                    $order->getCustomerGroupId(),
                    $newItem->getProductId()
                );

                $productIds .= $newItem->getProductId() . ',';
                $catalogruleProduct[$newItem->getProductId()] = $this->getCatalogRuleDiscountData($rules, $newItem);
                $salesRuleProduct[$newItem->getProductId()] = $this->getSalesRuleDiscountData($salesRuleIds, $newItem);
            }
            $data['product_ids'] = trim($productIds, ",");
            if (count($catalogruleProduct) > 0) {
                $data['catalogrule_product'] = json_encode($catalogruleProduct);
            }
            if (count($salesRuleProduct) > 0) {
                $data['salesrule'] = json_encode($salesRuleProduct);
            }
            $rowData->setData($data)->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e);

        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }

    /**
     * Set Discount data
     *
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData
     * @param AbstractItem $item
     * @return $this
     */
    protected function setDiscountData($discountData, $item)
    {
        $item->setDiscountAmount($discountData->getAmount());
        $item->setBaseDiscountAmount($discountData->getBaseAmount());

        return $this;
    }

    /**
     * get Discount data
     *
     * @param array $salesRuleIds
     * @param AbstractItem $item
     * @return $ruleOuput
     */
    protected function getSalesRuleDiscountData($salesRuleIds, $item)
    {
        $ruleOuput = [];
        foreach ($salesRuleIds as $key => $salesRuleId) {
            $ruleOuput[$key] = [];
            $rule = $this->salesRuleRepositoryInterface->getById($salesRuleId);
            $qty = $this->validatorUtility->getItemQty($item, $rule);

            $discountCalculator = $this->salesRuleCalcFactory->create($rule->getSimpleAction());
            $qty = $discountCalculator->fixQuantity($qty, $rule);
            $discountData = $discountCalculator->calculate($rule, $item, $qty);
            $this->validatorUtility->deltaRoundingFix($discountData, $item);

            $ruleOuput[$key]['qty'] = $qty;
            $ruleOuput[$key]['rule_id'] = $rule->getRuleId();
            $ruleOuput[$key]['rule_name'] = $rule->getName();
            $ruleOuput[$key]['discount'] = $discountData->getAmount();
            $ruleOuput[$key]['discount_step'] = $rule->getDiscountStep();
            $ruleOuput[$key]['simple_action'] = $rule->getSimpleAction();
            $ruleOuput[$key]['discount_amount'] = $rule->getDiscountAmount();
            $ruleOuput[$key]['coupon_type'] = $rule->getCouponType();
            $ruleOuput[$key]['apply_to_shipping'] = $rule->getApplyToShipping();

            $this->validatorUtility->minFix($discountData, $item, $qty);
            $this->setDiscountData($discountData, $item);
        }
        return $ruleOuput;
    }

    /**
     * get Catalog Rule Discount data
     *
     * @param array $rules
     * @param AbstractItem $item
     * @return array
     */
    protected function getCatalogRuleDiscountData($rules, $item)
    {
        if (!empty($rules) && ($countRules = count($rules))) {
            for ($i = 0; $i < $countRules; $i++) {
                $rule = [];
                $rule['rule_id'] = $rules[$i]['rule_id'];
                $rule['rule_name'] = $this->catalogRuleRepository->get($rules[$i]['rule_id'])->getName();
                $rule['action_operator'] = $rules[$i]['action_operator'];
                $rule['action_amount'] = $rules[$i]['action_amount'];
                $rule['sort_order'] = $rules[$i]['sort_order'];
                $rule['original_price'] = $item->getOriginalPrice();
                $rule['discount_calculation_price'] = $item->getDiscountCalculationPrice();
                $rule['original_discount_amount'] = $item->getOriginalDiscountAmount();
                $rule['qty_ordered'] = $item->getQty();
                $rules[$i] = $rule;
            }
        }
        return $rules;
    }
}
