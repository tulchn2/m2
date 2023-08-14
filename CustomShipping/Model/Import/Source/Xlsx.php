<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\CustomShipping\Model\Import\Source;

/**
 * Xlsx import adapter
 */
class Xlsx extends \Magento\ImportExport\Model\Import\AbstractSource
{
    /**
     * @var \Magento\Framework\Filesystem\File\Write
     */
    protected $_file;
    protected $_;

    /**
     * Delimiter.
     *
     * @var string
     */
    protected $_delimiter = ',';

    /**
     * @var string
     */
    protected $_enclosure = '';
    protected $_xlsxReader;

    /**
     * Open file and detect column names
     *
     * There must be column names in the first line
     *
     * @param string $file
     * @param string $delimiter
     * @param string $enclosure
     * @throws \LogicException
     */
    public function __construct(
        $file,
        $directory,
        $delimiter = ',',
        $enclosure = '"'
    ) {
        try {
            $this->_file = $directory->getActiveSheet()->toArray();
            $columnNames = array_shift($this->_file);
            foreach ($columnNames as $name) {
                switch ($name) {
                    case 'ID':
                        $name = 'region_id';
                        break;
                    case 'Country':
                        $name = 'country_id';
                        break;
                    case 'Name':
                        $name = 'default_name';
                        break;
                    case 'Shipping Cost':
                        $name = 'cost';
                        break;
                }
            }
        } catch (\Magento\Framework\Exception\FileSystemException $e) {
            throw new \LogicException("Unable to open file: '{$file}'");
        }
        if ($delimiter) {
            $this->_delimiter = $delimiter;
        }
        $this->_enclosure = $enclosure;

        parent::__construct($columnNames ?? []);
    }

    /**
     * Read next line from Xlsx-file
     *
     * @return array|bool
     */
    protected function _getNextRow()
    {
        if (count($this->_file) > 0) {
            $parsed = array_shift($this->_file);
        } else {
            $parsed = false;
        }
        return $parsed;
    }
}
