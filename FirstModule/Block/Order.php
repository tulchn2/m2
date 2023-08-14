<?php
namespace Ecommage\FirstModule\Block;
use Magento\Framework\View\Element\Template\Context;
use \Psr\Log\LoggerInterface;
class Order extends \Magento\Framework\View\Element\Template
{ 
    protected $_template = "Ecommage_FirstModule::order.phtml";
    protected $_orderCollectionFactory;
    protected $_orderConfig;
    protected $logger;
    protected $orderRepository;
    protected $searchCriteriaBuilder;

    public function __construct(
        Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory ,
        \Magento\Sales\Model\Order\Config $orderConfig,
        LoggerInterface $logger,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderConfig = $orderConfig;
        
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context);

    }


   public function getOrderCollection($filters)
   {
       $collection = $this->_orderCollectionFactory->create()
         ->addAttributeToSelect('*');
        foreach ($filters as $field => $condition) {
            $collection->addFieldToFilter($field, $condition);
        }
     
        return $collection;
    }


   public function getOrderCollectionByCustomerId($customerId)
   {
       $collection = $this->_orderCollectionFactory->create($customerId)
         ->addFieldToSelect('*')
         ->addFieldToFilter('status',
                ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
            )
         ->setOrder(
                'created_at',
                'desc'
            );
 
     return $collection;
    }
    public function getOrderCollectionRes() {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                'status',
                $this->_orderConfig->getVisibleOnFrontStatuses(),
                'in'
            )
            ->addFilter(
                'customer_email','roni_cost@example.com'
            )->create();

       return $this->orderRepository->getList($searchCriteria);
    }
}