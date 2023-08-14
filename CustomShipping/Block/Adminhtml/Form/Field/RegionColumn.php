<?php

namespace Ecommage\CustomShipping\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Directory\Api\CountryInformationAcquirerInterface;

class RegionColumn extends Select
{
    /**
     * @var CountryInformationAcquirerInterface
     */
    private $countryInformationAcquirerInterface;
    
    /**
     * RegionColumn constructor.
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Directory\Model\CountryInformationAcquirerInterface $countryInformationAcquirerInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        CountryInformationAcquirerInterface $countryInformationAcquirerInterface,
        array $data = []
    ) {
        $this->countryInformationAcquirerInterface = $countryInformationAcquirerInterface;

        parent::__construct($context, $data);
    }

    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    private function getSourceOptions(): array
    {
        $list = $this->countryInformationAcquirerInterface->getCountryInfo('VN');
        $option = [];

        foreach ($list->getAvailableRegions() as $region) {
            $option[] =['label' => $region->getName(), 'value' => $region->getId()];
        };

        return $option;
    }
}
