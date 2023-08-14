<?php

namespace Ecommage\CustomShipping\Controller\Adminhtml\Regions;

use Ecommage\CustomShipping\Model\ResourceModel\Regions\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Ecommage\CustomShipping\Model\ResourceModel\RegionsFrom\CollectionFactory as RegionFromCollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $filter;
    protected $collectionFactory;

    /**
     * Region From collection factory
     *
     * @var RegionFromCollectionFactory
     */
    private $regionFromCollectionFactory;
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        RegionFromCollectionFactory $regionFromCollectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->regionFromCollectionFactory = $regionFromCollectionFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $item) {
            $collection =$this->regionFromCollectionFactory->create()
            ->addFieldToFilter('region_id', $item->getRegionId())->load();
            $collection->walk('delete');
            $item->delete();
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
