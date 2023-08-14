<?php
namespace Ecommage\Banners\Controller\Adminhtml\Banners;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Ecommage\Banners\Model\BannersFactory
     */
    private $bannersFactory;
    const ADMIN_RESOURCE = 'Ecommage_Banners::Banners';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Ecommage\Banners\Model\BannersFactory $bannersFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Ecommage\Banners\Model\BannersFactory $bannersFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->bannersFactory = $bannersFactory;
    }

    /**
     * Mapped Banners List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->bannersFactory->create();
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getTitle();
            if (!$rowData->getId()) {
                $this->messageManager->addError(__('Banner data no longer exist.'));
                $this->_redirect('ecommage_banners/banners/index');
                return;
            }
        }

        $this->coreRegistry->register('ecommage_banner', $rowData);
        $title = $rowId ? __('Edit Banner Data: ').$rowTitle : __('Add Banner Data');
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
