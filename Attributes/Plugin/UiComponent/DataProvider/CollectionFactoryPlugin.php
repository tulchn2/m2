<?php
namespace Ecommage\Attributes\Plugin\UiComponent\DataProvider;

use Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory;
use Magento\Eav\Model\Attribute;
use Magento\Customer\Model\Customer;

class CollectionFactoryPlugin
{
    protected $eavAttribute;
    const CARD_ID = 'card_id';
    public function __construct(
        Attribute $eavAttribute,
    ) {
        $this->eavAttribute = $eavAttribute;
    }
    public function __afterGetReport(CollectionFactory $subject, $result, $requestName)
    {
        if ($requestName == 'ecommage_post_listing_data_source') {
            $attributeId = $this->eavAttribute->getIdByCode(Customer::ENTITY, self::CARD_ID);

            $attributeId && $result->getSelect()->joinLeft(
                ['thirdTable' => $result->getTable('customer_entity_varchar')],
                ('main_table.author_id = thirdTable.entity_id
                    AND thirdTable.attribute_id =' . $attributeId),
                [self::CARD_ID => 'thirdTable.value']
            );
        }

        return $result;
    }
}