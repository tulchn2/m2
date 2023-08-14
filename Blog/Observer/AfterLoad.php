<?php

namespace Ecommage\Blog\Observer;

class AfterLoad implements \Magento\Framework\Event\ObserverInterface
{
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$collection = $observer->getBlogsCollection();
        $stt = 1;
        foreach($collection as $post) {
            $post->setTitle('stt:'.$stt++.' -ID: '.$post->getId().': '.$post->getTitle());
            $post->setData('author_name', 'By: ' . $post->getFirstname() .  ' ' . $post->getData('lastname'));
        }
	}
}