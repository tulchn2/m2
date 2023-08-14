<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\CustomShipping\Controller\Adminhtml\Regions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use Ecommage\CustomShipping\Model\Import;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\ResourceConnection;

class ImportExcel extends \Magento\Backend\App\Action
{
    const TABLE = 'directory_country_region_cost';

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ecommage_CustomShipping::regions';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultPageFactory;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var XlsxReader
     */
    protected $_xlsxReader;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */

    /**
     * @var \Ecommage\CustomShipping\Model\Import
     */
    protected $importModel;

    /**
     * @var UploaderFactory
     */

    protected $_uploaderFactory;

    /**
     * @var ResourceConnection
     */
    protected $_resource;
    protected $_connection;
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        JsonHelper $jsonHelper,
        \Psr\Log\LoggerInterface $logger,
        XlsxReader $xlsxReader,
        UploaderFactory $uploaderFactory,
        ResourceConnection $resource,
        Import $importModel
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        $this->_xlsxReader = $xlsxReader;
        $this->importModel = $importModel;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->_uploaderFactory = $uploaderFactory;

        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        
        try {
            $files = $this->getRequest()->getFiles();
            if ($files) {
                $uploader = $this->_uploaderFactory->create(['fileId' => $this->importModel::FIELD_NAME_SOURCE_FILE]);
                $fileData = $uploader->save($this->importModel->getWorkingDir());
                $sourcePath = $fileData['path'] . $fileData['file'];
                $spreadsheet = $this->_xlsxReader->load($sourcePath);
                $dataSheet = $spreadsheet->getActiveSheet()->toArray();
                if ($this->saveEntityFinish($dataSheet)) {
                    $message = ['success' => __('The file was imported.')];
                }
            } else {
                $message = ['error' => __('The file was not uploaded.')];
            }
            return $this->jsonResponse($message);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }
    /**
     * Save entities
     *
     * @param array $dataSheet
     *
     * @return bool
     */
    private function saveEntityFinish(array $dataSheet)
    {
        if ($dataSheet) {
            $tableName = $this->_connection->getTableName(static::TABLE);
            $availableColumns = array_shift($dataSheet);
            $rows = [];
            $rowsFrom = [];
            foreach ($dataSheet as $row) {
                $columnValues = [];
                $columnValuesFrom = [];
                
                foreach ($availableColumns as $key => $columnKey) {
                    if ($columnKey == 'region_id') {
                        $columnValuesFrom[$columnKey] = $row[$key];
                    }
                    if ($columnKey == 'region_id_from' || $columnKey == 'cost_from') {
                        if ($columnKey == 'cost_from') {
                            $columnValuesFrom['cost'] = $row[$key];
                        } else {
                            $columnValuesFrom[$columnKey] = $row[$key];
                        }
                        continue;
                    }
                    $columnValues[$columnKey] = $row[$key];
                }
                $rows[] = $columnValues;
                $rowsFrom[] = $columnValuesFrom;

            }
            
            if ($rows) {
                $rows = array_map("unserialize", array_unique(array_map("serialize", $rows)));
                $this->_connection->insertOnDuplicate($tableName, $rows, array_keys($rows[0]));
                
                if ($rowsFrom) {
                    $tableNameFrom = $this->_connection->getTableName('from_country_region_cost');
                    $this->_connection->insertOnDuplicate($tableNameFrom, $rowsFrom, array_keys($rowsFrom[0]));
                }

                return true;
            }
        }
        return false;
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
