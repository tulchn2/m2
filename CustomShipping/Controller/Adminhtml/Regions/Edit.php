<?php
namespace Ecommage\CustomShipping\Controller\Adminhtml\Regions;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Ecommage\CustomShipping\Model\RegionsFactory
     */
    private $regionsFactory;
    const ADMIN_RESOURCE = 'Ecommage_CustomShipping::regions';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Ecommage\CustomShipping\Model\RegionsFactory $regionsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Ecommage\CustomShipping\Model\RegionsFactory $regionsFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->regionsFactory = $regionsFactory;
    }

    /**
     * Mapped Regions List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->regionsFactory->create();
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getDefaultName();
            if (!$rowData->getId()) {
                $this->messageManager->addError(__('Region data no longer exist.'));
                $this->_redirect('ecommage_shipping/regions/index');
                return;
            }
        }

        $this->coreRegistry->register('region_cost', $rowData);
        $title = $rowId ? __('Edit Region Data: ').$rowTitle : __('Add Region Data');
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
