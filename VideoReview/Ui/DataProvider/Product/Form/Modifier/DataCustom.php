<?php

namespace Ecommage\VideoReview\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;

/**
 * Data provider for "DataCustom" field of product page
 */
class DataCustom extends AbstractModifier
{
    protected $locator;
    protected $urlBuilder;
    protected $arrayManager;
    const GIF_ATTRIBUTE_CODE = 'gif_image';

    /**
     * @param LocatorInterface            $locator
     * @param UrlInterface                $urlBuilder
     * @param ArrayManager                $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
    }

    public function modifyMeta(array $meta)
    {
        $fieldCode = 'gif_image';
        $elementPath = $this->arrayManager->findPath(self::GIF_ATTRIBUTE_CODE, $meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(
            static::CONTAINER_PREFIX . self::GIF_ATTRIBUTE_CODE,
            $meta,
            null,
            'children'
        );

        if (!$elementPath) {
            return $meta;
        }

        $meta = $this->arrayManager->merge(
            $containerPath,
            $meta,
            [
                'children'  => [
                    $fieldCode => $this->getFileUploaderField()
                ]
            ]
        );

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();

        if ($modelId = $product->getId()) {
            $productData = $data[$modelId][self::DATA_SOURCE_DEFAULT];
            if (isset($productData[self::GIF_ATTRIBUTE_CODE]) && is_string($productData[self::GIF_ATTRIBUTE_CODE])) {
                $gifImage = $productData[self::GIF_ATTRIBUTE_CODE];
                $productData[self::GIF_ATTRIBUTE_CODE] = [];
                $productData[self::GIF_ATTRIBUTE_CODE][0]['url'] = $gifImage;
                $productData[self::GIF_ATTRIBUTE_CODE][0]['name'] = $gifImage;
                $data[$modelId][self::DATA_SOURCE_DEFAULT] = $productData;
            }
        }

        return $data;
    }

    public function getFileUploaderField()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'imageUploader',
                        'allowedExtensions' => 'jpg jpeg gif png',
                        'previewTmpl' => 'Magento_Catalog/image-preview',
                        'elementTmpl' => 'ui/form/element/uploader/image',
                        'formElement' => "imageUploader",
                        'dataScope' => "gif_image",
                        'dataType' => 'string',
                        'sortOrder' => 10,
                        'visible' => 1,
                        'uploaderConfig' => ['url' => 'ecommage_video_view/file/upload'],
                    ],
                ],
            ],
        ];
    }
}
