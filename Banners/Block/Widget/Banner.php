<?php
namespace Ecommage\Banners\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Block\Product\Context;
use Ecommage\Banners\Model\Banners;

class Banner extends Template implements BlockInterface
{

    /**
     * Default template
     */
    protected $_template = "widget/banner.phtml";

    /**
     * @var \Ecommage\Banners\Model\Banners
     */
    protected $bannerModel;

    /**
     * @var PostHelper
     */
    protected $postHelper;

    /**
     * Show Banner constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Ecommage\Banners\Model\Banners $bannerModel
     *
     */
    public function __construct(
        Context $context,
        Banners $bannerModel,
        array $data = []
    ) {
        $this->bannerModel = $bannerModel;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Show Banner constructor.
     *
     * @return null|Banners
     *
     */
    public function getBanner()
    {
        if (!$this->getBannerId()) {
            return null;
        }
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        $banner = $this->bannerModel->getCollection()
        ->addFieldToFilter('banner_id', ['eq' => $this->getBannerId()])
        ->addFieldToFilter('status', ['eq' => '1'])
        ->addFieldToFilter(
            'schedule_from',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['null' => true],
                ]
            ]
        )->addFieldToFilter(
            'schedule_to',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['null' => true],
                ]
            ]
        );
        return $banner;
    }

    public function getBannerId()
    {
        return $this->getData('banner_id');
    }
}
