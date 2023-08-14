<?php
namespace Ecommage\FirstModule\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
class AddProductCustomAtribute implements DataPatchInterface, PatchRevertableInterface
{
    private $_moduleDataSetup;

    private $_eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->_moduleDataSetup = $moduleDataSetup;
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $this->_moduleDataSetup]);

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'enable_color', [
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Enable Color',
            'input' => 'select',
            'class' => '',
            'source' => \Magento\Catalog\Model\Product\Attribute\Source\Boolean::class,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => true,
            // 'attribute_set'=> 'Top',
            // 'group' => 'Gift Options',
            // 'user_defined' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false,
        ]);
        $eavSetup->addAttributeToSet(\Magento\Catalog\Model\Product::ENTITY, 'Top', 'Gift Options', 'enable_color');
    }
    public function revert()
    {
        $this->_moduleDataSetup->getConnection()->startSetup();
        $customerSetup = $this->_eavSetupFactory->create(['setup' => $this->_moduleDataSetup]);
        $customerSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'enable_color');

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