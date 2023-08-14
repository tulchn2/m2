<?php
namespace Ecommage\Blog\Model;
use Ecommage\Blog\Model\ResourceModel\Blog\CollectionFactory;
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $loadedData;
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $postCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $postCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $blog = $this->collection->getItems();
        $this->loadedData = array();
        foreach ($blog as $post) {
            $rec = $post->getData();
            $rec['edit'] = true;
            $this->loadedData[$post->getId()]['blog_post'] = $rec;
            $this->loadedData[$post->getId()]['blog_post']['customer_name'] = $post->getData('firstname').' '.$post->getData('lastname');
        }


        return $this->loadedData;

    }
}