<?php
namespace Ecommage\CustomerReview\Block\Slider;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\Context;
use Ecommage\CustomerReview\Model\ResourceModel\Reviews\CollectionFactory;

class Review extends Template
{

    /**
     * Default template
     */
    protected $_template = "slider/customer-reivews.phtml";

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var PostHelper
     */
    protected $postHelper;

    /**
     * Show Banner constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Ecommage\CustomerReview\Model\ResourceModel\Reviews\CollectionFactory $collectionFactory
     *
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct(
            $context,
            $data
        );
    }
    /**
     * @return null|CollectionFactory
     */

    public function getCustomerReviews()
    {
        $collection = null;
        if ($productId = $this->getRequest()->getParam('id')) {
            $collection = $this->collectionFactory->create()
            ->addFieldToFilter('status_id', ['eq' => \Magento\Review\Model\Review::STATUS_APPROVED])
            ->addFieldToFilter('product_ids', ['finset' => $productId]);
        }
        return $collection;
    }
}
