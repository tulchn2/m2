<?php
namespace Ecommage\FirstModule\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Post extends AbstractModel implements IdentityInterface
{
	const CACHE_TAG = 'ecommage_helloworld_post';

	protected $_cacheTag = 'ecommage_helloworld_post';

	protected $_eventPrefix = 'ecommage_helloworld_post';

	protected function _construct()
	{
		$this->_init('Ecommage\FirstModule\Model\ResourceModel\Post');
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