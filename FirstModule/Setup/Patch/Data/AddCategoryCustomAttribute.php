<?php
namespace Ecommage\FirstModule\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
class AddCategoryCustomAttribute implements DataPatchInterface, PatchRevertableInterface
{
    private $_moduleDataSetup;

    private $_categorySetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->_moduleDataSetup = $moduleDataSetup;
        $this->_categorySetupFactory = $categorySetupFactory;
    }

    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_categorySetupFactory->create(['setup' => $this->_moduleDataSetup]);

        $eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'custom_attribute', [
            'type' => 'varchar',
            'label' => 'Custom Attribute Description',
            'input' => 'textarea',
            'required' => false,
            'sort_order' => 100,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'General Information',
            'is_used_in_grid' => true,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => true,
        ]);
    }
    public function revert()
    {
        $this->_moduleDataSetup->getConnection()->startSetup();
        $categorySetup = $this->_categorySetupFactory->create(['setup' => $this->_moduleDataSetup]);
        $categorySetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'custom_attribute');

        $this->_moduleDataSetup->getConnection()->endSetup();
    }
    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public static function getVersion()
    {
        return '1.0.0';
    }
}