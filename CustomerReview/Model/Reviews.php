<?php
namespace Ecommage\CustomerReview\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Ecommage\CustomerReview\Model\Source\City;

class Reviews extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'ecommage_review';
    protected $_eventPrefix = 'ecommage_review';
    protected $_eventObject = 'ecommage_review';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var City
     */
    private $_citySource;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ecommage\CustomerReview\Model\Source\City $citySource
     *
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        StoreManagerInterface $storeManager,
        City $citySource,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->_citySource = $citySource;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Ecommage\CustomerReview\Model\ResourceModel\Reviews::class);
        $this->setIdFieldName('review_id');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return string|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImageUrl()
    {
        $image = $this->getImage();
        if (!$image) {
            return false;
        }
        if (!is_string($image)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while getting the image url.')
            );
        }

        return $this->storeManager->getStore()->getBaseUrl(
            UrlInterface::URL_TYPE_WEB
        ) . $image;
    }

    /**
     * @return string|bool
     */
    public function getAddress()
    {
        $cityId = $this->getCityId();
        return $cityId ? $this->_citySource->getDefaultName($cityId) : false;
    }
}
