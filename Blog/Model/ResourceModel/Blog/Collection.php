<?php
namespace Ecommage\Blog\Model\ResourceModel\Blog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_eventPrefix = 'ecommage_blogs';
    protected $_eventObject = 'blogs_collection';
	protected function _construct()
	{
		$this->_init('Ecommage\Blog\Model\Blog', 'Ecommage\Blog\Model\ResourceModel\Blog');
	}
	
	protected function _initSelect()
	{
		parent::_initSelect();
		$this->getSelect()->joinLeft(
			['secondTable' => $this->getTable('customer_entity')],
			'main_table.author_id = secondTable.entity_id',
			['firstname', 'lastname']
		);
	}

}