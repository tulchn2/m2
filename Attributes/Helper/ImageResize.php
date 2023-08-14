<?php

namespace Ecommage\Attributes\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;

class ImageResize extends AbstractHelper
{
    protected $_filesystem;
    protected $_imageFactory;
    protected $_directory;
    protected $_storeManager;
    protected $_assetRepo;
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        AdapterFactory $imageFactory,
        StoreManagerInterface $storeManager,
        Repository $assetRepo,
    ) {
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
        $this->_assetRepo = $assetRepo;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        parent::__construct($context);
    }

    public function resizeImgProduct($imageName, $width = 258, $height = 200)
    {
        if(!is_string($imageName)) {
            return false;
        }
        /* Real path of image from directory */
        $realPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('catalog/product/' . $imageName);
        $fileSrcName = pathinfo($realPath, PATHINFO_BASENAME);
        if (!$this->_directory->isFile($realPath) || !$this->_directory->isExist($realPath)) {
            return $this->_assetRepo->getUrl('Magento_Catalog::images/product/placeholder/image.jpg');
        }
        /* Target directory path where our resized image will be save */
        $targetDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)
        ->getAbsolutePath('resized/' . $width . 'x' . $height);
        $pathTargetDir = $this->_directory->getRelativePath($targetDir);
        /* If Directory not available, create it */
        if (!$this->_directory->isExist($pathTargetDir)) {
            $this->_directory->create($pathTargetDir);
        }
        if (!$this->_directory->isExist($pathTargetDir)) {
            return false;
        }
        $image = $this->_imageFactory->create();
        $image->open($realPath);
        $image->keepAspectRatio(true);
        $image->resize($width, $height);
        $dest = $targetDir . '/' . $fileSrcName;
        $image->save($dest);
        if ($this->_directory->isFile($this->_directory->getRelativePath($dest))) {
            return $this->_storeManager->getStore()->getBaseUrl(
                UrlInterface::URL_TYPE_MEDIA
                ) . 'resized/' . $width . 'x' . $height . '/' . $fileSrcName;
        }
        return false;
    }
}