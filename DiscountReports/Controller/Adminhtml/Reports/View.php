<?php

namespace Ecommage\DiscountReports\Controller\Adminhtml\Reports;

use Magento\Framework\Controller\ResultFactory;

class View extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Ecommage\DiscountReports\Model\ReportsFactory
     */
    private $reportsFactory;
    const ADMIN_RESOURCE = 'Ecommage_DiscountReports::reports';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Ecommage\DiscountReports\Model\ReportsFactory $ReportsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Ecommage\DiscountReports\Model\ReportsFactory $reportsFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->reportsFactory = $reportsFactory;
    }

    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->reportsFactory->create();
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getIncrementId();
            $this->coreRegistry->register('discount_report', $rowData);
            $resultPage->getConfig()->getTitle()->prepend(__('Discount Reports Data: #').$rowTitle);
            if (!$rowData->getReportId()) {
                $this->messageManager->addError(__('Discount Reports data no longer exist.'));
                $this->_redirect('ecommage_discount/reports/index');
                return;
            }
        }
        return $resultPage;
    }
}
