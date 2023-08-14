<?php
namespace Ecommage\CustomerReview\Controller\Form;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;
use Ecommage\CustomerReview\Model\ReviewsFactory;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Controller\ResultFactory;

class Post extends \Magento\Framework\App\Action\Action
{
    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var AdapterFactory
     */
    protected $adapterFactory;
    
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var \Ecommage\CustomerReview\Model\ReviewsFactory
     */
    protected $reviewsFactory;
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param UserContextInterface $userContext
     * @param UploaderFactory $uploaderFactory
     * @param AdapterFactory $adapterFactory
     * @param Filesystem $filesystem
     * @param ReviewsFactory $reviewsFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        UserContextInterface $userContext,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem,
        ReviewsFactory $reviewsFactory
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->reviewsFactory = $reviewsFactory;
        $this->adapterFactory = $adapterFactory;
        $this->userContext = $userContext;
        $this->filesystem = $filesystem;

        return parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $files = $this->getRequest()->getFiles();
        $customerId = $this->userContext->getUserId();
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        if (!$data || !$customerId) {
            return $resultRedirect;
        }
        if (isset($files['image']) && !empty($files['image']["name"])) {
            try {
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'image']);
                //check upload file type or extension
                $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $imageAdapter = $this->adapterFactory->create();
                $uploaderFactory->addValidateCallback('custom_image_upload', $imageAdapter, 'validateUploadFile');
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);
                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('media/wysiwyg/customer');
                $result = $uploaderFactory->save($destinationPath);
                if (!$result) {
                    throw new  \Magento\Framework\Exception\LocalizedException(
                        __('Image cannot be saved to path: $1', $destinationPath)
                    );
                }
                $imagePath = 'media/wysiwyg/customer'.$result['file'];
                
                $rowData = $this->reviewsFactory->create();
                //Set file path with name for save into database
                $data['image'] = $imagePath;
                $data['status_id'] = \Magento\Review\Model\Review::STATUS_PENDING;
                $data['author_id'] = $customerId;
                $rowData->setData($data)->save();
                $this->messageManager->addSuccess(__('Post review success!'));
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));

            }
        }
        return $resultRedirect;
    }
}
