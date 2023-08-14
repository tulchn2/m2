<?php
namespace Ecommage\SliderCategoriesWidget\Block\Widget\Category;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Helper\Output;

class Slider extends Template implements BlockInterface
{

    /**
     * Default template
     */
    protected $_template = "widget/slider.phtml";

    /**
     * @var CategoryCollectionFactory;
     */
    protected $_categoryCollectionFactory;

    /**
     * @var StoreManagerInterface;
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Helper\Output;
     */
    protected $_catalogHelperOutput;

    /**
     * Slider category constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory;
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager;
     * @param \Magento\Catalog\Helper\Output $catalogHelperOutput;
     *
     */
    public function __construct(
        Template\Context $context,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        Output $catalogHelperOutput,
        array $data = []
    ) {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_catalogHelperOutput = $catalogHelperOutput;

        parent::__construct($context, $data);
    }

    /**
     * Filter for selected categories
     *
     * @return $collection branch
     */

    public function getCategoryCollection()
    {
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'description', 'thumbnail', 'image']);
        $collection->addAttributeToFilter('entity_id', ['in' => $this->getCategories()]);
        return $collection;
    }

    /**
     * Return ids in config
     *
     * @return string
     */
    public function getCategories()
    {
        return $this->getData('multi_category_chooser');
    }

    /**
     * convert category thumbnail URL
     *
     * @param string
     * @return string
     */
    public function getThumbnailUrl($imageName)
    {
        $url = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . 'catalog/category/' . $imageName;
        return $url;
    }

    /**
     * Prepare category attribute html output
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param string $attributeHtml
     * @param string $attributeName
     * @return string
     */
    public function categoryAttribute($category, $attributeHtml, $attributeName)
    {
        return $this->_catalogHelperOutput->categoryAttribute($category, $attributeHtml, $attributeName);
    }
}
