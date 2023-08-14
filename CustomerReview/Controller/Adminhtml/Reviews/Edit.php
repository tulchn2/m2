<?php
namespace Ecommage\CustomerReview\Controller\Adminhtml\Reviews;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Ecommage\CustomerReview\Model\ReviewsFactory
     */
    private $reviewsFactory;
    const ADMIN_RESOURCE = 'Ecommage_CustomerReview::CustomerReview';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Ecommage\CustomerReview\Model\ReviewsFactory $reviewsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Ecommage\CustomerReview\Model\ReviewsFactory $reviewsFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->reviewsFactory = $reviewsFactory;
    }

    /**
     * Mapped CustomerReview List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->reviewsFactory->create();
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getTitle();
            if (!$rowData->getId()) {
                $this->messageManager->addError(__('Review data no longer exist.'));
                $this->_redirect('ecommage_reviews/reviews/index');
                return;
            }
        }

        $this->coreRegistry->register('ecommage_review', $rowData);
        $title = $rowId ? __('Edit Review Data: ').$rowTitle : __('Add Review Data');
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
