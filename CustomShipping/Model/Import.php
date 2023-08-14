<?php

namespace Ecommage\CustomShipping\Model;

use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;

class Import extends \Magento\ImportExport\Model\Import
{

    /**
     * Limit displayed errors on Import History page.
     */
    const LIMIT_VISIBLE_ERRORS = 5;

    /**
     * @var \Ecommage\CustomShipping\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $_timezone;

    /**
     * @var \Ecommage\CustomShipping\Model\Source\Type\AbstractType
     */
    protected $_source;

    /**
     * @var \Magento\ImportExport\Model\History
     */
    protected $importHistoryModel;

    /**
     * @param \Ecommage\CustomShipping\Helper\Data                         $helper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface       $timezone
     * @param \Psr\Log\LoggerInterface                                   $logger
     * @param \Magento\Framework\Filesystem                              $filesystem
     * @param \Magento\ImportExport\Helper\Data                          $importExportData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface         $coreConfig
     * @param \Magento\ImportExport\Model\Import\ConfigInterface         $importConfig
     * @param \Magento\ImportExport\Model\Import\Entity\Factory          $entityFactory
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data      $importData
     * @param \Magento\ImportExport\Model\Export\Adapter\CsvFactory      $csvFactory
     * @param \Magento\Framework\HTTP\Adapter\FileTransferFactory        $httpFactory
     * @param \Magento\MediaStorage\Model\File\UploaderFactory           $uploaderFactory
     * @param \Magento\ImportExport\Model\Source\Import\Behavior\Factory $behaviorFactory
     * @param \Magento\Framework\Indexer\IndexerRegistry                 $indexerRegistry
     * @param \Magento\ImportExport\Model\History                        $importHistoryModel
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                $localeDate
     * @param array                                                      $data
     */
    public function __construct(
        \Ecommage\CustomShipping\Helper\Data $helper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig,
        \Magento\ImportExport\Model\Import\ConfigInterface $importConfig,
        \Magento\ImportExport\Model\Import\Entity\Factory $entityFactory,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\ImportExport\Model\Export\Adapter\CsvFactory $csvFactory,
        \Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\ImportExport\Model\Source\Import\Behavior\Factory $behaviorFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\ImportExport\Model\History $importHistoryModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $localeDate,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->_timezone = $timezone;
        $this->importHistoryModel = $importHistoryModel;

        parent::__construct(
            $logger,
            $filesystem,
            $importExportData,
            $coreConfig,
            $importConfig,
            $entityFactory,
            $importData,
            $csvFactory,
            $httpFactory,
            $uploaderFactory,
            $behaviorFactory,
            $indexerRegistry,
            $importHistoryModel,
            $localeDate,
            $data
        );
        $this->_debugMode = $helper->getDebugMode();
    }

    /**
     * Prepare source type class name
     *
     * @param $sourceType
     * @return string
     */
    protected function _prepareSourceClassName($sourceType)
    {
        return 'Ecommage\CustomShipping\Model\Source\Type\\' . ucfirst(strtolower($sourceType));
    }

    /**
     * Check if remote file was modified since the last import
     *
     * @param $timestamp
     * @return bool
     */
    public function checkModified($timestamp)
    {
        if ($this->getSource()) {
            return $this->getSource()->checkModified($timestamp);
        }
        return true;
    }

    /**
     * Download remote source file to temporary directory
     *
     * @TODO change the code to show exceptions on frontend instead of 503 error.
     * @return null|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function uploadSource()
    {
        $result = null;
        if ($this->getImportSource() && $this->getImportSource() != 'file') {
            $source = $this->getSource();
            try {
                $result = $source->uploadSource();
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            }
        } else {
            $uploader = $this->_uploaderFactory->create(['fileId' => self::FIELD_NAME_SOURCE_FILE]);
            $extension = $uploader->getFileExtension();
            
            if ($extension == 'xlsx') {
                $fileData = $uploader->save($this->getWorkingDir());
                $result = $fileData['path'] . $fileData['file'];
            } elseif ($extension == 'txt') {
                $fileData = $uploader->save($this->getWorkingDir());
                $result = $fileData['path'] . $fileData['file'];
            }
        }

        if ($result) {
            $sourceFileRelative = $this->_varDirectory->getRelativePath($result);
            $entity             = $this->getEntity();
            $this->createHistoryReport($sourceFileRelative, $entity);

            return $result;
        }

        return parent::uploadSource();
    }

    /**
     * Validates source file and returns validation result.
     *
     * @param \Magento\ImportExport\Model\Import\AbstractSource $source
     * @return bool
     */
    public function validateSource(\Magento\ImportExport\Model\Import\AbstractSource $source)
    {
        $this->addLogComment(__('Begin data validation'));
        try {
            $adapter = $this->_getEntityAdapter()->setSource($source);
            $errorAggregator = $adapter->validateData();
        } catch (\Exception $e) {
            $errorAggregator = $this->getErrorAggregator();
            $errorAggregator->addError(
                \Magento\ImportExport\Model\Import\Entity\AbstractEntity::ERROR_CODE_SYSTEM_EXCEPTION
                    . '. ' . $e->getMessage(),
                ProcessingError::ERROR_LEVEL_CRITICAL,
                null,
                null,
                null,
                $e->getMessage()
            );
        }

        $messages = $this->getOperationResultMessages($errorAggregator);
        $this->addLogComment($messages);

        $result = !$errorAggregator->getErrorsCount();
        if ($result) {
            $this->addLogComment(__('Import data validation is complete.'));
        } else {
            if ($this->isReportEntityType()) {
                $this->importHistoryModel->load($this->importHistoryModel->getLastItemId());
                $summary = '';
                if ($errorAggregator->getErrorsCount() > self::LIMIT_VISIBLE_ERRORS) {
                    $summary = __('Too many errors. Please check your debug log file.') . '<br />';
                } else {
                    if ($this->getJobId()) {
                        $summary = __('Import job #' . $this->getJobId() . ' failed.') . '<br />';
                    }

                    foreach ($errorAggregator->getRowsGroupedByErrorCode() as $errorMessage => $rows) {
                        $error = $errorMessage . ' ' . __('in rows') . ': ' . implode(', ', $rows);
                        $summary .= $error . '<br />';
                    }
                }
                $date = $this->_timezone->formatDateTime(
                    new \DateTime(),
                    \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::MEDIUM,
                    null,
                    null
                );
                $summary .= '<i>' . $date . '</i>';
                $this->importHistoryModel->setSummary($summary);
                $this->importHistoryModel->setExecutionTime(\Magento\ImportExport\Model\History::IMPORT_FAILED);
                $this->importHistoryModel->save();
            }
        }
        return $result;
    }

    /**
     * @return \Ecommage\CustomShipping\Model\Source\Type\AbstractType
     */
    public function getSource()
    {
        if (!$this->_source) {
            $sourceType = $this->getImportSource();
            try {
                $this->_source = $this->_helper->getSourceModelByType($sourceType);
                $this->_source->setData($this->getData());
            } catch (\Exception $e) {
                
            }
        }

        return $this->_source;
    }

    /**
     * @return mixed
     */
    public function getImportHistoryModel()
    {
        return $this->importHistoryModel;
    }

    /**
     * createHistoryReport
     *
     * @param string $sourceFileRelative
     * @param string $entity
     * @param null   $extension
     * @param null   $result
     *
     * @return $this
     */
    public function createHistoryReport($sourceFileRelative, $entity, $extension = null, $result = null)
    {
        return parent::createHistoryReport($sourceFileRelative, $entity, $extension = null, $result = null);
    }
}
