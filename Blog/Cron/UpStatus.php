<?php

namespace Ecommage\Blog\Cron;
use Ecommage\Blog\Model\ResourceModel\Blog\CollectionFactory;
use Psr\Log\LoggerInterface;
class UpStatus
{
    protected $collectionFactory;
    protected $_logger;
    public function __construct(
        CollectionFactory $collectionFactory,
        LoggerInterface $logger)
    {
        $this->collectionFactory = $collectionFactory;
        $this->_logger = $logger;
    }

	public function execute()
	{
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/blog.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);    
        try {
            $logger->info('Updated  ' . $this->updateStatus(). ' posts');
		} catch (\Exception $e) {
            $logger->info('Error cron: ' . $e->getMessage());
		}


		return $this;
	}

    public function updateStatus(){
        $collection = $this->collectionFactory
            ->create()
            ->addFieldToFilter('status', array('neq' => '1'));
        $i = 0;
        foreach ($collection as $item) {
            $item->setStatus('1')->save();
            $i ++;
        }
        return $i;
    }
}