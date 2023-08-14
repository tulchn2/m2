<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Ecommage\Blog\Controller\Adminhtml\Post;

use Ecommage\Blog\Model\ResourceModel\Blog\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
/**
 * Class MassStatus
 * @package Ecommage\Blog\Controller\Adminhtml\Post
 */
class MassStatus extends \Magento\Backend\App\Action
{
    protected $filter;
    protected $collectionFactory;
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $statusValue = $this->getRequest()->getParam('status');
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $item) {
            $item->setStatus($statusValue);
            $item->save();
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were changed.', $collectionSize));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
