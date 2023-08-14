<?php

namespace Ecommage\CustomShipping\Controller\Adminhtml\Regions;

use Magento\Framework\App\ResourceConnection;
use Ecommage\CustomShipping\Model\ResourceModel\RegionsFrom\CollectionFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Ecommage\CustomShipping\Model\RegionsFactory
     */
    protected $regionsFactory;

    /**
     * @var \Ecommage\CustomShipping\Model\ResourceModel\RegionsFrom\CollectionFactory
     */
    protected $regionsFromCollectionFactory;

    /**
     * @var ResourceConnection
     */
    protected $_resource;
    protected $_connection;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ecommage\CustomShipping\Model\RegionsFactory $regionsFactory
     * @param \Ecommage\CustomShipping\Model\ResourceModel\RegionsFrom\ResourceConnection $regionsFromCollectionFactory
     *
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        ResourceConnection $resource,
        \Ecommage\CustomShipping\Model\RegionsFactory $regionsFactory
    ) {
        parent::__construct($context);
        $this->regionsFactory = $regionsFactory;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('ecommage_shipping/regions/edit');
            return;
        }
        try {
            $rowData = $this->regionsFactory->create();
            $rowData->setData($data)->save();
            $tableName = $this->_connection->getTableName('from_country_region_cost');

            if (!empty($data['advanced_costs'])) {
                $rows = [];
                $availableColumns = [
                    'region_id_from' , 'cost', 'region_id'
                ];
    
                foreach ($data['advanced_costs'] as $cost) {
                    $costTmp['region_id_from'] = $cost['region_id_from'];
                    $costTmp['cost'] = $cost['cost'];
                    $costTmp['region_id'] = $rowData->getRegionId();
                    if (array_search($cost['region_id_from'], array_column($rows, 'region_id_from'), true) === false) {
                        $rows[] = $costTmp;
                    }
                }
                if (count($rows)) {
                    if (!empty($data['region_id_from_list'])) {
                        $listDelete = array_diff($data['region_id_from_list'], array_column($rows, 'region_id_from'));
                        if ($listDelete) {
                            $regionFrom = $this->_connection->quoteInto('region_id_from IN (?)', $listDelete);
                            $regionTo = $this->_connection->quoteInto('region_id = ?', $rowData->getRegionId());
                            $condition = new \Zend_Db_Expr($regionFrom . ' AND ' . $regionTo);
                            $this->_connection->delete($tableName, $condition);
                        }
                    }
                    $this->_connection->insertOnDuplicate($tableName, $rows, $availableColumns);
                }
            }
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('ecommage_shipping/regions/edit', ['id' => $rowData->getRegionId()]);
                return;
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('ecommage_shipping/regions/index');
    }
}
