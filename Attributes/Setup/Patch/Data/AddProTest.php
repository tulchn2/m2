<?php
namespace Ecommage\Attributes\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
class AddProTest implements DataPatchInterface, PatchRevertableInterface
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
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'test_ba');
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'test_ba', [
            'label' => 'aaa Ecommagea aaaaaaaaaaa',
            'input' => 'text',
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'class' => '',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'unique' => false,
        ]);
        $eavSetup->addAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'Top', 'Gift Options');
        $eavSetup->addAttributeToSet(
            \Magento\Catalog\Model\Product::ENTITY, 'Top', 'Gift Options',
            'test_ba',
            '10'
        );
    }
    public function revert()
    {
        $this->_moduleDataSetup->getConnection()->startSetup();
        $proSetup = $this->_eavSetupFactory->create(['setup' => $this->_moduleDataSetup]);
        $proSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'ecommage_taaaaaa');

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