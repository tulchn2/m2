<?php
namespace Ecommage\Attributes\Plugin\Block\Blog\Slider;

use Ecommage\Blog\Block\Index;
use Magento\Eav\Model\Attribute;
use Magento\Customer\Model\Customer;

class CollectionPlugin
{
    protected $eavAttribute;
    const CARD_ID = 'card_id';
    public function __construct(
        Attribute $eavAttribute,
    ) {
        $this->eavAttribute = $eavAttribute;
    }
    public function beforeToHtml(Index $subject)
    {
        if ($subject->getTemplate() == 'Ecommage_Blog::slider.phtml') {
            $subject->setTemplate('Ecommage_Attributes::sliderAuthor.phtml');
        }
    }
    public function afterGetBlogPublishedCollection(Index $subject, $result)
    {
        $attributeId = $this->eavAttribute->getIdByCode(Customer::ENTITY, self::CARD_ID);

        $attributeId && $result->getSelect()->joinLeft(
            ['thirdTable' => $result->getTable('customer_entity_varchar')],
            ('main_table.author_id = thirdTable.entity_id
                AND thirdTable.attribute_id =' . $attributeId),
            [self::CARD_ID => 'thirdTable.value']
        );

        return $result;
    }
}