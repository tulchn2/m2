<?php
namespace Ecommage\CustomerReview\Block\Modal;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\Context;
use Ecommage\CustomerReview\Model\ResourceModel\Reviews\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\Authorization\Model\UserContextInterface;
use Ecommage\CustomerReview\Model\Source\City;

class Review extends Template
{
    /**
     * Default template
     */
    protected $_template = "modal/customer-reivews.phtml";

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var OrderItemCollectionFactory
     */
    protected $orderItemsCollectionFactory;
    /**
     * @var UserContextInterface
     */
    private $userContext;
    /**
     * @var City
     */
    private $_citySource;
    /**
     * Show Banner constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Ecommage\CustomerReview\Model\ResourceModel\Reviews\CollectionFactory $collectionFactory
     * @param \Ecommage\CustomerReview\Model\Source\City $citySource
     *
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        OrderItemCollectionFactory $orderItemsCollectionFactory,
        UserContextInterface $userContext,
        City $citySource,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->orderItemsCollectionFactory = $orderItemsCollectionFactory;
        $this->userContext = $userContext;
        $this->_citySource = $citySource;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @return array
     */
    public function getCityList()
    {
        return $this->_citySource->toOptionArray();
    }

    /**
     * @return bool
     */

    public function isShowModalAddReview()
    {
        if (($productId = $this->getCurrentProductId())
            && ($customerId = $this->userContext->getUserId())) {

            $collection = $this->orderItemsCollectionFactory->create();
            $collection->addFieldToSelect(['order_id','product_id'])
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('secondTable.customer_id', $customerId)
                ->addFieldToFilter('thirdTable.product_ids', ['finset' => $productId])
                ->getSelect()->joinLeft(
                    ['secondTable' => $collection->getTable('sales_order')],
                    'main_table.order_id = secondTable.entity_id',
                    ['customer_id']
                )->joinLeft(
                    ['thirdTable' => $collection->getTable('ecommage_reviews')],
                    'secondTable.customer_id = thirdTable.author_id',
                    ['review_id','product_ids']
                )->distinct(true);
            $data = $collection->getFirstItem();
            if (!$data->getData() || (empty($data->getReviewId()) && !empty($data->getOrderId()))) {
                return true;
            }
        }
        return false;
    }
    /**
     * @return null|int
     */

    public function getCurrentProductId()
    {
        if ($this->getRequest()->getParam('id')) {
            return  $this->getRequest()->getParam('id');
        }
        return null;
    }
}
