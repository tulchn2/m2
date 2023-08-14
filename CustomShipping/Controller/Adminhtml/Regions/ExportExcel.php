<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\CustomShipping\Controller\Adminhtml\Regions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Ecommage\CustomShipping\Model\Export\ConvertToXls;

class ExportExcel extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ecommage_CustomShipping::regions';

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var ConvertToXls
     */
    protected $converter;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Ecommage\CustomShipping\Model\Export\ConvertToXls $ConvertToXls
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        ConvertToXls $converter
    ) {
        $this->_fileFactory = $fileFactory;
        $this->converter = $converter;
        parent::__construct($context);
    }

    /**
     * Export regions grid to XLS format
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        return $this->_fileFactory->create('regions-cost.xls', $this->converter->getXlsFile(), DirectoryList::VAR_DIR);
    }
}
