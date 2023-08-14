<?php

namespace Ecommage\Banners\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Action extends Column
{
    /** Url path */
    const ROW_EDIT_URL = 'ecommage_banners/banners/edit';
    const ROW_DELETE_URL = 'ecommage_banners/banners/delete';
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['banner_id'])) {
                    $item[$name] = [
                        'edit' => [
                            'href' => $this->_urlBuilder->getUrl(
                                self::ROW_EDIT_URL,
                                ['id' => $item['banner_id']]
                            ),
                            'label' => __('Edit')
                        ],
                        'remove' => [
                            'href' => $this->_urlBuilder->getUrl(
                                self::ROW_DELETE_URL,
                                ['id' => $item['banner_id']]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete Post'),
                                'message' => __('Are you sure you want to delete this banner?')
                            ]
                        ],
                    ];
                }
            }
        }
        return $dataSource;
    }
}
