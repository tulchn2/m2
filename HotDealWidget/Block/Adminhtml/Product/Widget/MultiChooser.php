<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Product Chooser for "Product Link" Cms Widget Plugin
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Ecommage\HotDealWidget\Block\Adminhtml\Product\Widget;

use Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Widget\Grid\Extended;

class MultiChooser extends Chooser
{

    /**
     * Initialize child blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'choose_button',
            $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class)->setData(
                [
                    'label' => __('Choose selected'),
                    'onclick' => $this->getJsObjectName() . '.doChoose()',
                    'class' => 'task',
                ]
            )
        );
        return parent::_prepareLayout();
    }

    /**
     * Generate export button
     *
     * @return string
     */
    public function getChooseButtonHtml()
    {
        return $this->getChildHtml('choose_button');
    }

    /**
     * Generate list of grid buttons
     *
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = '';
        if ($this->getFilterVisibility()) {
            $html .= $this->getSearchButtonHtml();
            $html .= $this->getResetFilterButtonHtml();
            $html .= '<br /><br />';
            $html .= $this->getChooseButtonHtml();
        }
        return $html;
    }

    /**
     * Massaction node onClick listener js function
     *
     * @return string
     */
    public function getAdditionalJavascript()
    {
        $chooserJsObject = $this->getId();
        $js =
            '
            {jsObject}.initChecked = function(){
                $$("#' . $chooserJsObject . '_table tbody input:checkbox").each(function(element, i){
                    const values = ' . $chooserJsObject . '.getElementValue();
                    var capture = values.replace("{"+element.value+"}", "match");
                    if(capture.search("match") != -1){
                        element.checked = true;
                   }
                });
            }
            {jsObject}.initChecked();
            const values = ' . $chooserJsObject . '.getElementValue();
            const bottomInput = "<input type=\"hidden\" value="+values+" name=\"selected_products\" />";
            $("' . $chooserJsObject . '").insert({bottom: "<div class=\"filter\">"+bottomInput+"</div>"});
            $$("#' . $chooserJsObject . '_table tbody input:checkbox").invoke("observe", "change", function(event){
                var element = Event.element(event);
                var label = element.up("td").next().next().next().innerHTML;
                label = label.replace(/^\s\s*/, "").replace(/\s\s*$/, "");
            if(element.checked)
                {
                    {jsObject}.addValue(element.value);
                    {jsObject}.addLabel(label);
                }
                else
                {
                    {jsObject}.removeValue(element.value);
                    {jsObject}.removeLabel(label);
                }
            });
            {jsObject}.removeValue = function(value){
                var currentValue =  ' . $chooserJsObject . '.getElementValue();
                currentValue =  currentValue.replace("{"+value+"}", "");
                ' . $chooserJsObject . '.setElementValue(currentValue);
            }
            {jsObject}.addValue = function(value){
                var currentValue = ' . $chooserJsObject . '.getElementValue();
                currentValue = currentValue.replace("{"+value+"}", "");
                currentValue = currentValue + "{"+value+"}";
                ' . $chooserJsObject . '.setElementValue(currentValue);
            }
            {jsObject}.removeLabel = function(label){
                var currentLabel =  ' . $chooserJsObject . '.getElementLabelText();
                currentLabel = currentLabel.replace(label+", ", "");
                ' . $chooserJsObject . '.setElementLabel(currentLabel);
            }
            {jsObject}.addLabel = function(label){
                var currentLabel = ' . $chooserJsObject . '.getElementLabelText();
                if(currentLabel.search("Not Selected") != -1){
                    currentLabel = currentLabel.replace("Not Selected", "");
                }
                if(currentLabel.search(label+", ") != -1){
                    currentLabel = currentLabel.replace(label +", ", "");
                }
                currentLabel = currentLabel + label +", ";
                ' . $chooserJsObject . '.setElementLabel(currentLabel);
            }
                {jsObject}.doChoose = function(node,e){
                    ' . $chooserJsObject . '.close();
                }
            ';
        $js = str_replace('{jsObject}', $this->getJsObjectName(), $js);
        return $js;
    }

    /**
     * Apply special Price filtering to collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /* @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_collectionFactory->create()->setStoreId(0)->addAttributeToSelect('name');
         $collection->addStoreFilter()->addAttributeToFilter(
             'special_price',
             [ "notnull" => true ]
         );
        if ($categoryId = $this->getCategoryId()) {
            $category = $this->_categoryFactory->create()->load($categoryId);
            if ($category->getId()) {
                $productIds = $category->getProductsPosition();
                $productIds = array_keys($productIds);
                if (empty($productIds)) {
                    $productIds = 0;
                }
                $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
            }
        }

        if ($productTypeId = $this->getProductTypeId()) {
            $collection->addAttributeToFilter('type_id', $productTypeId);
        }

        $this->setCollection($collection);
        return Extended::_prepareCollection();
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element Form Element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $massaction = $this->getUseMassaction();
        $sourceUrl = $this->getUrl(
            'ecommage_widget/product_widget/chooser',
            ['uniq_id' => $uniqId, 'use_massaction' => $massaction]
        );

        $chooser = $this->getLayout()->createBlock(
            \Magento\Widget\Block\Adminhtml\Widget\Chooser::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        if ($element->getValue()) {
            $value = explode('/', $element->getValue());
            $productId = false;
            if (isset($value[0]) && isset($value[1]) && $value[0] == 'product') {
                $productId = $value[1];
            }

            $categoryId = isset($value[2]) ? $value[2] : false;
            $label = '';

            if ($categoryId) {
                $label = $this->_resourceCategory->getAttributeRawValue(
                    $categoryId,
                    'name',
                    $this->_storeManager->getStore()
                ) . '/';
            }

            if ($this->getUseMassaction()) {
                $ids = explode('}{', $element->getValue());
                $cleanIds = [];
                foreach ($ids as $id) {
                    $id = str_replace(['{', '}'], '', $id);
                    $cleanIds[] = $id;
                }
                $products = $this->_getProductsByIDs($cleanIds);
                if ($products) {
                    foreach ($products as $product) {
                        $label .= $product->getName() . ', ';
                    }
                }
            } elseif ($productId) {
                $label .= $this->_resourceProduct->getAttributeRawValue(
                    $productId,
                    'name',
                    $this->_storeManager->getStore()
                );
            }
            $chooser->setLabel($label);
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Get Product collection by array ids
     *
     * @param array
     * @return $collection
     */
    protected function _getProductsByIDs($productIds)
    {
        $collection = $this->_collectionFactory->create()->addIdFilter($productIds);
        $collection->addAttributeToSelect('name');
        return $collection;
    }

    /**
     * Adds additional parameter to URL for loading only products grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'ecommage_widget/product_widget/chooser',
            [
                'products_grid' => true,
                '_current' => true,
                'uniq_id' => $this->getId(),
                'use_massaction' => $this->getUseMassaction(),
                'product_type_id' => $this->getProductTypeId()
            ]
        );
    }
}
