<?php
namespace Ecommage\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Blog extends AbstractModel implements IdentityInterface


{
	const CACHE_TAG = 'ecommage_blogs';
	protected $_eventPrefix = 'ecommage_post';

	protected $_eventObject = 'ecommage_post';

	protected function _construct()
	{
		$this->_init('Ecommage\Blog\Model\ResourceModel\Blog');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}