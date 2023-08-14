<?php<?php

/**
 * @var \Ecommage\Banners\Block\Widget\Banner $block
 * @var \Magento\Framework\Escaper $escaper
 * @codingStandardsIgnoreFile
 */

$banner = $block->getBanner();
?>
<?php if ($banner): ?>
    <div class="custom-banner-wrap">
        <a href="<?= $escaper->escapeUrl($banner->getUrlKey()) ?>" class="custom-banner-link">
            <img
                src="<?= /* @noEscape */ $banner->getImageUrl() ?>"
                alt="<?= $escaper->escapeHtml($banner->getTitle()) ?>"
                title="<?= $escaper->escapeHtml($banner->getTitle()) ?>"
                >
        </a>
    </div>
<?php endif; ?>

namespace Ecommage\VideoReview\Block\Product;

use Magento\Framework\View\Element\Template\Context;
use Magento\Reports\Model\ResourceModel\Product\Collection;
use Magento\Framework\Registry;

class CountView extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Reports\Model\ResourceModel\Product\Collection
     */
    private $reportCollection;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    public function __construct(
        Context $context,
        Collection $reportCollection,
        Registry $registry
    ) {
        $this->reportCollection = $reportCollection;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }
    public function getCurrentProduct()
    {
        return $this->_coreRegistry->registry('product');
    }
    public function getCountView()
    {
        $product = $this->getCurrentProduct();
        $productId = $product->getId();
        $productReport = $this->reportCollection
            ->setProductAttributeSetId($product->getAttributeSetId())
            ->addViewsCount()
            ->addFieldToFilter('entity_id', $productId);

        if (count($productReport) > 0) {
            foreach ($productReport as $productViewed) {
                if ($productViewed['entity_id'] == $productId) {
                    return (int) $productViewed['views'];
                }
            }
        }
        return 0;
    }
}
