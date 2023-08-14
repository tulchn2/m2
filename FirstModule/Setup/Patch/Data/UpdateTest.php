<?php
namespace Ecommage\FirstModule\Setup\Patch\Data;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
class UpdateTest implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }
    /**
     * Add eav attributes
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->updateAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'enable_color',
            [
                'frontend_input' => 'text',
                'backend_type' => 'varchar'
            ]
        );
    }
    /**
     * Get dependencies
     */
    public static function getDependencies()
    {
        return [];
    }
    /**
     * Get Aliases
     */
    public function getAliases()
    {
        return [];
    }
}