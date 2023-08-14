<?php

namespace Ecommage\Banners\Controller\Adminhtml\Banners;

use Magento\Framework\Url\Validator;
use Laminas\Uri\UriFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Ecommage\Banners\Model\BannersFactory
     */
    protected $bannersFactory;
    protected $_coreRegistry = null;
    private $_validator;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ecommage\Banners\Model\BannersFactory $bannersFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ecommage\Banners\Model\BannersFactory $bannersFactory,
        \Magento\Framework\Registry $registry,
        Validator $validator,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_validator = $validator;

        parent::__construct($context);
        $this->bannersFactory = $bannersFactory;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $data = $this->getRequest()->getParam('general');
        if (!$data) {
            $this->_redirect('ecommage/banners/edit');
            return;
        }
        try {
            if (!$this->_validator->isValid($data['url_key'])) {
                $this->messageManager->addErrorMessage(__('Please use valid url'));
                $id = (int)$this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('ecommage_banners/banners/edit', ['id' => $id]);
                } else {
                    $this->_redirect('ecommage_banners/banners/newAction');
                }
                return;
            }
            $rowData = $this->bannersFactory->create();
            $rowData->setData($this->_filterPostData($data))->save();
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('ecommage_banners/banners/edit', ['id' => $rowData->getBannerId()]);
                return;
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('ecommage_banners/banners/index');
    }
    
    protected function _filterPostData(array $rawData)
    {
        $data = $rawData;
        unset($data['updated_at']);
        if (isset($data['image']) && is_array($data['image'])) {
            if (isset($data['image'][0]['name']) && isset($data['image'][0]['url'])) {
                $data['image'] = UriFactory::factory($data['image'][0]['url'])->getPath();
            } else {
                unset($data['image']);
            }
        }
        if (isset($data['date_group']['schedule_from'])) {
            $data['schedule_from'] = $data['date_group']['schedule_from'];
        }
        if (isset($data['date_group']['schedule_to'])) {
            $data['schedule_to'] = $data['date_group']['schedule_to'];
        }
        return $data;
    }
}
