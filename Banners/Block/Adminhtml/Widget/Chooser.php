<?php

namespace Ecommage\Banners\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Ecommage\Banners\Model\ResourceModel\Banners\CollectionFactory;
use Ecommage\Banners\Model\Banners;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Framework\Exception\LocalizedException;

class Chooser extends Extended
{
    const BANNERS_WIDGET_CHOOSER_PATH = 'ecommage_banners/banners/chooser';

    /**
     * @var \Ecommage\Banners\Model\ResourceModel\Banners\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Ecommage\Banners\Model\Banners
     */
    protected $bannerModel;

    /**
     * Banner constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ecommage\Banners\Model\ResourceModel\Banners\CollectionFactory $collectionFactory
     * @param \Ecommage\Banners\Model\Banners $bannerModel
     *
     * @param array   $data
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        CollectionFactory $collectionFactory,
        Banners $bannerModel,
        $data = []
    ) {
        $this->collectionFactory = $collectionFactory->create();
        $this->bannerModel = $bannerModel;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Block construction, prepare grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('banner_id');
        $this->setUseAjax(true);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element
     *
     * @return AbstractElement
     * @throws LocalizedException
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl(
            self::BANNERS_WIDGET_CHOOSER_PATH,
            ['uniq_id' => $uniqId]
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

        if ($value = $element->getValue()) {
            $label = '';
            if ($banner = $this->bannerModel->load($value)) {
                $label = $banner->getTitle();
            }
            $chooser->setLabel($label);
        }
        $element->setData('after_element_html', $chooser->toHtml());

        return $element;
    }

    /**
     * Prepare columns for banners grid
     *
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'banner_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'banner_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'title',
            [
                'header' => __('Banner Title'),
                'name' => 'title',
                'index' => 'title',
                'header_css_class' => 'col-title',
                'column_css_class' => 'col-title'
            ]
        );
        $this->addColumn(
            'schedule_from',
            [
                'header' => __('From'),
                'name' => 'schedule_from',
                'index' => 'schedule_from',
                'header_css_class' => 'col-schedule_from',
                'column_css_class' => 'col-schedule_from'
            ]
        );
        $this->addColumn(
            'schedule_to',
            [
                'header' => __('To'),
                'name' => 'schedule_to',
                'index' => 'schedule_to',
                'header_css_class' => 'col-schedule_to',
                'column_css_class' => 'col-schedule_to'
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        return '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var bannerId = trElement.down("td").innerHTML;
                var optionLabel = trElement.down("td").next().innerHTML;
                var optionValue = bannerId.replace(/^\s+|\s+$/g,"");
                ' .
            $chooserJsObject .
            '.setElementValue(optionValue);
                ' .
            $chooserJsObject .
            '.setElementLabel(optionLabel);
                ' .
            $chooserJsObject .
            '.close();
            }
        ';
    }

    /**
     * @return $this|Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->addFieldToFilter('status', ['eq' => 1]);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Adds additional parameter to URL for loading only banners grid
     *
     * @return string
     */
    public function getGridUrl(): string
    {
        return $this->getUrl(self::BANNERS_WIDGET_CHOOSER_PATH, ['_current' => true]);
    }
}
