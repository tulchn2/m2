<?php

namespace Ecommage\Validation\Observer;

class CheckLogout implements \Magento\Framework\Event\ObserverInterface
{
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
        $customer = $observer->getData('customer');
        return false;
	}
}