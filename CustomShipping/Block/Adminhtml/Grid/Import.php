<?php

namespace Ecommage\CustomShipping\Block\Adminhtml\Grid;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Import implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        return [
            'label' => __('Import Xlsx'),
            'class' => 'primary',
            'id' => 'import-button',
            'for' => 'import_file',
            'on_click' => 'document.getElementById("import_file").click();'
        ];
    }
}
