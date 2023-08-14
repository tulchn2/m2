<?php

namespace Ecommage\Banners\Model;

use Ecommage\Banners\Model\ResourceModel\Banners\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $loadedData;
    private $coreRegistry;
    protected $storeManager;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $postCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $postCollectionFactory->create();
        $this->coreRegistry = $coreRegistry;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $currentBanner = $this->coreRegistry->registry('ecommage_banner');
        $data = $currentBanner->getData();
        if (!empty($data)) {
            if (isset($data['image'])) {
                unset($data['image']);
                $data['image'][0]['name'] = $currentBanner->getImage();
                $data['image'][0]['url'] = $currentBanner->getImageUrl();
            }
            $data['date_group']['schedule_from'] = isset($data['schedule_from'])
                ? $currentBanner->getScheduleFrom()
                : null;
            $data['date_group']['schedule_to'] = isset($data['schedule_to']) ? $currentBanner->getScheduleTo() : null;
            $banner = $this->collection->getNewEmptyItem();
            $banner->setData($data);
            $this->loadedData[$banner->getId()]['general'] = $banner->getData();
        }
        return $this->loadedData;
    }
}
