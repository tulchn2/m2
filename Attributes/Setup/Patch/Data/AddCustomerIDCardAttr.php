<?php
namespace Ecommage\Attributes\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Ui\Model\ResourceModel\Bookmark\Collection as BookmarkCollection;

class AddCustomerIDCardAttr implements DataPatchInterface, PatchRevertableInterface, PatchVersionInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var CustomerSetup
     */
    private $customerSetupFactory;
    /**
     * @var SetFactory
     */
    private $attributeSetFactory;

    private $bookmarkCollection;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param SetFactory $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory     $customerSetupFactory,
        SetFactory               $attributeSetFactory,
        BookmarkCollection       $bookmarkCollection
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->bookmarkCollection = $bookmarkCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        // $this->moduleDataSetup->getConnection()->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'card_id',
            [
                'label' => 'ID Card',
                'input' => 'text',
                'type' => 'varchar',
                'source' => '',
                'required' => false,
                'position' => 40,
                'visible' => true,
                'system' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => false,
                'backend' => ''
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'card_id');
        $attribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer'],
            //you can use other forms also [
            //      adminhtml_checkout
            //      adminhtml_customer
            //      adminhtml_customer_address
            //      checkout_register
            //      customer_account_create
            //      customer_account_edit
            //      customer_address_edit
            //      customer_register_address
            // ]
            
        ]);
        $attribute->save();
        $this->deleteOrderGridBookmarks();

        // $this->moduleDataSetup->getConnection()->endSetup();
    }

    private function deleteOrderGridBookmarks()
    {
        // Get collection and connection
        $connection = $this->bookmarkCollection->getConnection();

        // Get ID's to delete
        $ids = $this->bookmarkCollection->addFieldToFilter('namespace', [
            'in' => ['customer_listing', 'ecommage_post_listing']
        ])->getColumnValues('bookmark_id');

        // Delete
        $connection->delete(
            $this->bookmarkCollection->getMainTable(),
            $connection->quoteInto('bookmark_id IN(?)', $ids)
        );
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->removeAttribute(Customer::ENTITY, 'card_id');

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
    public static function getVersion(){
        return '1.1.0';
    }
}