<?php

namespace Ecommage\CustomerReview\Controller\Adminhtml\Reviews;

use Laminas\Uri\UriFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Ecommage\CustomerReview\Model\ReviewsFactory
     */
    protected $reviewsFactory;
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ecommage\CustomerReview\Model\ReviewsFactory $reviewsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ecommage\CustomerReview\Model\ReviewsFactory $reviewsFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->_coreRegistry = $registry;

        parent::__construct($context);
        $this->reviewsFactory = $reviewsFactory;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('ecommage_reviews/reviews/edit');
            return;
        }
        try {
            $rowData = $this->reviewsFactory->create();
            $rowData->setData($this->_filterPostData($data))->save();
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('ecommage_reviews/reviews/edit', ['id' => $rowData->getReviewId()]);
                return;
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('ecommage_reviews/reviews/index');
    }
    
    protected function _filterPostData(array $rawData)
    {
        $data = $rawData;
        unset($data['links']);
        unset($data['updated_at']);
        unset($data['created_at']);

        if (isset($data['image']) && is_array($data['image'])) {
            if (isset($data['image'][0]['name']) && isset($data['image'][0]['url'])) {
                $data['image'] = UriFactory::factory($data['image'][0]['url'])->getPath();
            } else {
                unset($data['image']);
            }
        }
        if (!empty($data['products'])) {
            $data['product_ids'] = implode(",", array_column($data['products'], 'entity_id'));
            unset($data['products']);
        }
        
        return $data;
    }
}
