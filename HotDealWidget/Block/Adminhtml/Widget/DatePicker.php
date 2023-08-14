<?php

namespace Ecommage\HotDealWidget\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\AbstractElement;

class DatePicker extends \Magento\Backend\Block\Widget
{

    /**
     * @var Factory
     */
    private $elementFactory;

    /**
     * DatePicker constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array   $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        $data = []
    ) {
        $this->elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }
    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element Form Element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $showTime = $this->getshowTime() ? $this->getshowTime() : false;
        /** @var \Magento\Framework\Data\Form\Element\Text $input */
        $input = $this->elementFactory->create("text", ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->addCustomAttribute('style', 'width: auto');
        $input->setClass('widget-option input-text admin__control-text');
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }

        $calendarScript = '
            <script>require([
                "jquery",
                "mage/translate",
                "mage/calendar"
                ], function ($, $t) {
                $("#' . $element->getId() . '").calendar({
                    dateFormat: "M/d/yy",
                    timeFormat: "HH:mm:ss",
                    showsTime: '.$showTime.',
                    showButtonPanel: true,
                    currentText: $t("Go Today"),
                    closeText: $t("Close")
                });
                })</script>';
        $element->setData('after_element_html', $input->getElementHtml() . $calendarScript);
        $element->setValue('');

        return $element;
    }
}
