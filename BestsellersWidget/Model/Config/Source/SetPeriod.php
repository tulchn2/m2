<?php

namespace Ecommage\BestsellersWidget\Model\Config\Source;

use Magento\Sales\Model\ResourceModel\Report\Bestsellers;

class SetPeriod implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            [
                'value' => Bestsellers::AGGREGATION_DAILY,
                'label' => __('Daily')
            ],
            [
                'value' => Bestsellers::AGGREGATION_MONTHLY,
                'label' => __('Monthly')
            ],
            [
                'value' => Bestsellers::AGGREGATION_YEARLY,
                'label' => __('Yearly')
            ]
        ];
        return $options;
    }
}
