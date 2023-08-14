<?php

namespace Ecommage\FirstModule\Observer;

class SetCustomerLogin implements \Magento\Framework\Event\ObserverInterface
{
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
                $customer = $observer->getCustomer();
                $originalName = $customer->getFirstname();
                // $customer->getCustomAttribute('my_custom_attribute')->getValue() not working
                if($customNewAttribute = $customer->getResource()->getAttribute('custom_new_attribute')){
                    $customNewAttribute = $customNewAttribute->getFrontend()->getValue($customer);
                }
                $modifiedName = $customNewAttribute." ".$originalName;
                $customer->setFirstname($modifiedName);
	}
}