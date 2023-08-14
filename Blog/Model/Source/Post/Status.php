<?php

namespace Ecommage\Blog\Model\Source\Post;

/**
 * Post Status source model
 * @package Ecommage\Blog\Model\Source\Post
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    // Statuses to store in DB
    const PUBLICATION = 1;
    const DRAFT = 2;
    const NON_PUBLISH = 3;

    /**
     * @var array
     */
    private $options;

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            self::PUBLICATION => __('Published'),
            self::DRAFT => __('Draft'),
            self::NON_PUBLISH => __('non-publish')
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            foreach ($this->getOptions() as $value => $label) {
                $this->options[] = ['value' => $value, 'label' => $label];
            }
        }
        return $this->options;
    }
}