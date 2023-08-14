<?php

namespace Ecommage\CustomerReview\Model;

use Magento\Framework\Registry;
use Ecommage\CustomerReview\Model\ResourceModel\Reviews\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Helper\Image as ImageHelper;
use Ecommage\CustomerReview\Ui\Utils\Price as PriceModifier;
use Magento\Customer\Api\CustomerRepositoryInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $loadedData;
    /**
     * @var Registry
     */
    private $coreRegistry;
    /**
     * @var ProductCollectionFactory
     */
    private $_productCollectionFactory;
    /**
     * @var ImageHelper
     */
    private $_imageHelper;

    /**
     * @var PriceModifier
     */
    private $_priceModifier;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $postCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        ImageHelper $imageHelper,
        PriceModifier $priceModifier,
        Registry $coreRegistry,
        CustomerRepositoryInterface $customerRepository,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $postCollectionFactory->create();
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_imageHelper = $imageHelper;
        $this->_priceModifier = $priceModifier;
        $this->coreRegistry = $coreRegistry;
        $this->_customerRepository = $customerRepository;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $currentReview = $this->coreRegistry->registry('ecommage_review');
        $data = $currentReview->getData();
        if (!empty($data)) {
            if (isset($data['image'])) {
                unset($data['image']);
                $data['image'][0]['name'] = $currentReview->getImage();
                $data['image'][0]['url'] = $currentReview->getImageUrl();
            }
            if (!empty($data['product_ids'])) {
                $productIds = explode(',', $data['product_ids']);
                $data['links']['products'] = $this->getReviewProducts($productIds);
            }
            if (!empty($data['author_id'])) {
                $customer = $this->_customerRepository->getById($data['author_id']);
                if ($customer->getId()) {
                    $data['author'] =
                    $customer->getFirstname(). " " . $customer->getLastname() . " ID: " . $customer->getId();
                }
            }
            $review = $this->collection->getNewEmptyItem();
            $review->setData($data);
            $this->loadedData[$review->getId()] = $review->getData();
        }
        return $this->loadedData;
    }

    /**
     * @param int $questionId
     *
     * @return array|null
     */
    private function getReviewProducts($productIds)
    {
        if ($productIds) {
            $productCollection = $this->_productCollectionFactory->create();
            $productCollection->addIdFilter($productIds)
                ->addAttributeToSelect(['status', 'thumbnail', 'name', 'price'], 'left');

            $result = [];
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            foreach ($productCollection->getItems() as $product) {
                $result[] = $this->fillData($product);
            }

            return $result;
        }

        return null;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return array
     */
    private function fillData(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        return [
            'entity_id' => $product->getId(),
            'thumbnail' => $this->_imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
            'name' => $product->getName(),
            'status' => $product->getStatus(),
            'type_id' => $product->getTypeId(),
            'sku' => $product->getSku(),
            'price' => $product->getPrice() ? $this->_priceModifier->toDefaultCurrency($product->getPrice()) : ''
        ];
    }
}
