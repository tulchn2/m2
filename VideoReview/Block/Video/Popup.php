<?php

namespace Ecommage\VideoReview\Block\Video;

class Popup extends \Magento\Catalog\Block\Product\View\AbstractView
{
    public function getListTitle()
    {
        $items = [
            [
                'name' => __('View Photos'),
            ],
        ];
        $currentProduct = $this->getProduct();
        $items = $this->callPopupOption('Video 360', $currentProduct->getGifImage(), $items);
        $items = $this->callPopupOption('Video Rivew', $currentProduct->getLinkReview(), $items);

        return $items;
    }
    protected function callPopupOption($title, $val, $arr = null)
    {
        if ($val) {
            $arr[] = [
                'name' => $title,
                'url' => $val,
                'is_popup' => true
            ];
        };
        return $arr;
    }
}
