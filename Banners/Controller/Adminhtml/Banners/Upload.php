<?php

namespace Ecommage\Banners\Controller\Adminhtml\Banners;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Model\ImageUploader;

class Upload extends \Magento\Backend\App\Action
{

    const BASE_TMP_PATH = "wysiwyg/ecommage/";
    const BASE_PATH = "wysiwyg/ecommage/";

    /**
     * Image uploader
     *
     * @var \Magento\Catalog\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * Upload constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Model\ImageUploader $imageUploader
     *
     */
    public function __construct(
        Context $context,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * @return mixed
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ecommage_Banners::banners');
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        try {
            $imageId = $this->_request->getParam('param_name', 'image');
            $this->imageUploader->setAllowedExtensions([]);
            $this->imageUploader->setBaseTmpPath(self::BASE_TMP_PATH);
            $this->imageUploader->setBasePath(self::BASE_PATH);
            $result = $this->imageUploader->saveFileToTmpDir($imageId);
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
