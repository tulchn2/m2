<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ecommage\VideoReview\Controller\Adminhtml\File;

use Magento\Catalog\Model\ImageUploader;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Model\ProductFactory;

/**
 * Class Ecommage\VideoReview\Controller\Adminhtml\FileUpload
 */
class Upload extends \Magento\Backend\App\Action
{

    /**
     * Image uploader
     *
     * @var \Magento\Catalog\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * @var \Magento\Catalog\Model\ProductFactory;
     */
    protected $productFactory;

    /**
     * Upload constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Model\ImageUploader $imageUploader
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        ProductFactory $productFactory,
        Context $context,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->imageUploader = $imageUploader;
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $imageId = $this->_request->getParam('param_name', 'image');

        if ((int) $this->_request->getParam('product') > 0) {
            $productId = (int) $this->_request->getParam('product');
            $product = $this->productFactory->create()->load($productId);
            $product->setStoreId(0)->setData($this->_request->getParam('code'), null)->save();
        }

        try {
            $this->imageUploader->setAllowedExtensions([]);
            $this->imageUploader->setBaseTmpPath('catalog/tmp/attribute-data');
            $this->imageUploader->setBasePath('catalog/attribute-data');

            $result = $this->imageUploader->saveFileToTmpDir($imageId);
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain()
            ];
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            ];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
