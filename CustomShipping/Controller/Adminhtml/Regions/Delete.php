<?php

namespace Ecommage\CustomShipping\Controller\Adminhtml\Regions;

use Magento\Backend\App\Action;
use Ecommage\CustomShipping\Model\ResourceModel\RegionsFrom\CollectionFactory as RegionFromCollectionFactory;
use Ecommage\CustomShipping\Model\RegionsFromFactory;

class Delete extends Action
{

    private $regionsFactory;
    const ADMIN_RESOURCE = 'Ecommage_CustomShipping::regions';

    /**
     * Region From collection factory
     *
     * @var RegionFromCollectionFactory
     */
    private $regionFromCollectionFactory;

    /**
     * Region From factory
     *
     * @var RegionsFromFactory
     */
    private $regionFromFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ecommage\CustomShipping\Model\RegionsFactory $regionsFactory,
        RegionFromCollectionFactory $regionFromCollectionFactory,
        RegionsFromFactory $regionFromFactory
    ) {
        parent::__construct($context);
        $this->regionsFactory = $regionsFactory;
        $this->regionFromFactory = $regionFromFactory;
        $this->regionFromCollectionFactory = $regionFromCollectionFactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $rowData = $this->regionsFactory->create();

        if (!($region = $rowData->load($id))) {
            $this->messageManager->addError(__('Unable to proceed. Please, try again.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index', ['_current' => true]);
        }
        try {
            $collection =$this->regionFromCollectionFactory->create()
            ->addFieldToFilter('region_id', $region->getRegionId())->getAllIds();
            $regionFrom = $this->regionsFactory->create();
            foreach ($collection as $item) {
                $regionFrom->load($item)->delete();
            }
            $region->delete();
            $this->messageManager->addSuccess(__('Your regions has been deleted !'));
        } catch (Exception $e) {
            $this->messageManager->addError(__('Error while trying to delete regions: '));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index', ['_current' => true]);
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }
}
