<?php
namespace Ecommage\CustomShipping\Helper;

use Ecommage\CustomShipping\Model\Source\Factory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;

class Data extends AbstractHelper
{
    const GENERAL_DEBUG = 'Ecommage_CustomShipping/general/debug';

    /**
     * @var ScopeConfigInterface
     */
    protected $coreConfig;

    /**
     * Import source type factory model
     *
     * @var Factory
     */
    protected $sourceFactory;

    /**
     * Data Helper constructor
     *
     * @param Context $context
     * @param Factory $sourceFactory
     */
    public function __construct(
        Context $context,
        Factory $sourceFactory
    ) {
        $this->coreConfig = $context->getScopeConfig();
        $this->sourceFactory = $sourceFactory;
        parent::__construct($context);
    }

    /**
     * Prepare source type class name
     *
     * @param string $sourceType
     *
     * @return string
     */
    protected function prepareSourceClassName($sourceType)
    {
        return 'Ecommage\CustomShipping\Model\Source\Type\\' . ucfirst(strtolower($sourceType));
    }

    /**
     * Get source model by source type
     *
     * @param string $sourceType
     *
     * @return \Ecommage\CustomShipping\Model\Source\Type\AbstractType
     * @throws LocalizedException
     */
    public function getSourceModelByType($sourceType)
    {
        $sourceClassName = $this->prepareSourceClassName($sourceType);
        if ($sourceClassName && class_exists($sourceClassName)) {
            /* @var $source \Ecommage\CustomShipping\Model\Source\Type\AbstractType */
            $source = $this->getSourceFactory()->create($sourceClassName);
            return $source;
        } else {
            throw new LocalizedException(
                __("Import source type class for '" . $sourceType . "' is not exist.")
            );
        }
    }

    /**
     * Get source factory
     *
     * @return Factory
     */
    public function getSourceFactory()
    {
        return $this->sourceFactory;
    }

    /**
     * @return bool
     */
    public function getDebugMode()
    {
        return (bool)$this->coreConfig->getValue(
            self::GENERAL_DEBUG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
