<?php

namespace Ecommage\VideoReview\Model\ProductAttribute\Backend;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;

class FileUpload extends AbstractBackend
{

    /**
     * @var string
     */
    private $additionalData = '_additional_data_';

    /**
     * Gets image data from $value array.
     * Will return empty string in a case when $value is not an array
     *
     * @param array $value Attribute value
     * @return string
     */
    private function getUploadedImageData($value, $name = 'name')
    {
        if (is_array($value) && isset($value[0][$name])) {
            return $value[0][$name];
        }

        return '';
    }

    /**
     * Avoiding saving potential upload data to DB
     * Will set empty image attribute value if image was not uploaded
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     * @since 101.0.8
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();
        
        if ($value = $object->getData($attributeName)) {
            if ($imageName = $this->getUploadedImageData($value, 'url')) {
                $object->setData($this->additionalData . $attributeName, $value);
                $object->setData($attributeName, $imageName);
            } elseif (!is_string($value)) {
                $object->setData($attributeName, '');
            }
        } else {
            $object->setData($attributeName, '');
        }

        return parent::beforeSave($object);
    }
}
