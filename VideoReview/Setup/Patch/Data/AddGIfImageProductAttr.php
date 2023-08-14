<?php

namespace Ecommage\VideoReview\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Catalog\Model\Product;

class AddGIfImageProductAttr implements DataPatchInterface, PatchRevertableInterface
{

    private $_moduleDataSetup;

    private $_eavSetupFactory;

    const GIF_ATTRIBUTE_CODE = 'gif_image';
    const LINK_VIDEO_ATTRIBUTE_CODE = 'link_review';

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
        $eavSetup->addAttribute(Product::ENTITY, self::GIF_ATTRIBUTE_CODE, [
            'type' => 'varchar',
            'label' => 'Video 360',
            'input' => 'image',
            'backend' => \Ecommage\VideoReview\Model\ProductAttribute\Backend\FileUpload::class,
            'required' => false,
            'sort_order' => 10,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'Video Review',
            'user_defined' => false,
            "class" => "",
            "note" => "Up file GIF"
        ]);
        $eavSetup->addAttribute(Product::ENTITY, self::LINK_VIDEO_ATTRIBUTE_CODE, [
            'label' => 'Link Video Review',
            'input' => 'text',
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'class' => '',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'group' => 'Video Review',
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'unique' => false,
        ]);

        $attributeSetIds = $eavSetup->getAllAttributeSetIds(Product::ENTITY);

        foreach ($attributeSetIds as $attributeSetId) {
            $eavSetup->addAttributeGroup(
                Product::ENTITY,
                $attributeSetId,
                'Video Review',
                '2'
            );
        }
    }
    public function revert()
    {
        $this->_moduleDataSetup->getConnection()->startSetup();
        $proSetup = $this->_eavSetupFactory->create(['setup' => $this->_moduleDataSetup]);
        $proSetup->removeAttribute(Product::ENTITY, self::GIF_ATTRIBUTE_CODE);
        $proSetup->removeAttribute(Product::ENTITY, self::LINK_VIDEO_ATTRIBUTE_CODE);
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
