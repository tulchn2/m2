<?php
namespace Ecommage\FirstModule\Block;

use Magento\Framework\View\Element\Template\Context;
use Ecommage\FirstModule\Helper\Data;
use Ecommage\FirstModule\Model\PostFactory;
use Magento\Framework\App\Response\Http;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\CustomerRepository;

class Index extends \Magento\Framework\View\Element\Template
{
	protected $helperData;
	protected $_postFactory;
	protected $response;
	// protected $_template = 'Ecommage_FirstModule::first_show.phtml';
	protected $_customer;
    protected $_customerFactory;
	protected $_customerRepository;
	public function __construct(
		Context $context,
		PostFactory $postFactory,
		Data $helperData,
		Http $response,
		CustomerFactory $customerFactory,
		CustomerRepository $customerRepository,
		Customer $customers)
	{
		$this->helperData = $helperData;
		$this->_postFactory = $postFactory;
		$this->response = $response;
		$this->_customerFactory = $customerFactory;
		$this->_customerRepository = $customerRepository;
        $this->_customer = $customers;
		parent::__construct($context);
	}

	public function sayHello()
	{
		return __('Hello World 1');
	}
	public function getTextFirstModule()
	{
		// return get_class($this); isSet
		// return $this->getData('template');
		if($this->helperData->getGeneralConfig('enable')) {
			return $this->helperData->getGeneralConfig('display_text');
		}
		return $this->response->setRedirect('/ecommage/');
	}
	public function getPostCollection(){
		$post = $this->_postFactory->create();
		
		return $post->getCollection();
	}

    public function getCustomerCollection() {
        return $this->_customer->getCollection()
               ->addAttributeToSelect("*")
               ->load();
    }

    public function getFilteredCustomerCollection() {
        return $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("name")
                ->addAttributeToFilter("email", array("eq" => "tmail2@gmail.com"));
    }
	public function __getCustomerDetail() {
		$customer = $this->_customerFactory->create()->load('3');
		return $customer;
	}
	public function getCustomerDetail() {
		$customer = $this->_customer->load('3');
		return $customer;
	}

	public function getCustomer()
	{   
		return $this->_customerRepository->getById('3');
	}

	public function getCustomerByEmail($customerEmail)
	{   
		return $this->_customerRepository->get($customerEmail);
	}
	
}