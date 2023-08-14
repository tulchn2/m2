<?php

namespace Ecommage\Attributes\Observer;

use Magento\Eav\Model\Attribute;
use Magento\Customer\Model\Customer;

class GridAfterLoad implements \Magento\Framework\Event\ObserverInterface
{
    protected $eavAttribute;
    const CARD_ID = 'card_id';
    public function __construct(
        Attribute $eavAttribute,
    ) {
        $this->eavAttribute = $eavAttribute;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getBlogsCollection();
        $attributeId = $this->eavAttribute->getIdByCode(Customer::ENTITY, self::CARD_ID);

        $collection->getSelect()->joinLeft(
            ['thirdTable' => $collection->getTable('customer_entity_varchar')],
            'main_table.author_id = thirdTable.entity_id AND thirdTable.attribute_id =' . $attributeId,
            [self::CARD_ID => 'thirdTable.value']
        );
    }
}