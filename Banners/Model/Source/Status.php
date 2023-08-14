<?php

namespace Ecommage\Banners\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Ecommage\Banners\Model\Banners;

class Status implements OptionSourceInterface
{
    /**
     * @var Banners
     */
    private $banner;

    /**
     * @var array
     */
    private $options;

    /**
     * Status constructor.
     * @param \Ecommage\Banners\Model\Banners $banner
     */
    public function __construct(Banners $banner)
    {
        $this->banner = $banner;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            $availableOptions = $this->banner->getAvailableStatuses();
            foreach ($availableOptions as $value => $label) {
                $this->options[] = ['value' => $value, 'label' => $label];
            }
        }
        return $this->options;
    }
}
