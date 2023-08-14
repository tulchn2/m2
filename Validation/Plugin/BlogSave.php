<?php
 
namespace Ecommage\Validation\Plugin;

use Ecommage\Blog\Controller\Post\Save;
use Magento\Framework\Message\ManagerInterface;
use Ecommage\Validation\Helper\Data;
class BlogSave
{
    protected $_messageManager;
    protected $_helper;
    protected $_resultRedirectFactory;
    public function __construct(
        ManagerInterface $messageManager,
        Data $helper
        )
    {
        $this->_helper = $helper;
        $this->_messageManager = $messageManager;
    }

    public function beforeExecute(Save $subject)
    {
        $isCheckWords = $this->_helper->getGeneralConfig('enable');
        if($isCheckWords){
            $input = $subject->getRequest()->getPostValue();
            $badWords = $isCheckWords ? $this->_helper->getGeneralConfig('bad_words') : '';
            $badWords =  preg_replace('/\s*[,.;\/|]\s*/', '|', $badWords);
            
            $matches = array();
            $matchFound = preg_match_all(
                "/\b(" . $badWords . ")\b/i",
                $input['content'], 
                $matches
            );

            if ($matchFound) {
                if (isset($input['editRecordId'])) {
                    $subject->getRequest()->setPostValue([
                        'editRecordId' => $input['editRecordId'],
                        'error_message' => __('Bad words accquired, please update')
                    ]);
                }else{
                    $subject->getRequest()->setPostValue([]);
                    $this->_messageManager->addErrorMessage(__('Bad words accquired, please update'));
                }
            }
        }
    }

    public function __aroundExecute(Save $subject, \Closure $proceed)
    {
        $isCheckWords = $this->_helper->getGeneralConfig('enable');
        if($isCheckWords){
            $input = $subject->getRequest()->getPostValue();
            $badWords = $isCheckWords ? $this->_helper->getGeneralConfig('bad_words') : '';
            $badWords =  preg_replace('/\s*[,.;\/|]\s*/', '|', $badWords);
            
            $matches = array();
            $matchFound = preg_match_all(
                "/\b(" . $badWords . ")\b/i",
                $input['content'], 
                $matches
            );
            
            if ($matchFound) {
                if (isset($input['editRecordId'])) {
                    $subject->getRequest()->setPostValue([
                        'editRecordId' => $input['editRecordId'],
                        'error_message' => __('Bad words accquired, please update')
                    ]);
                    $input = $subject->getRequest()->getPostValue();
                }else{
                    $subject->getRequest()->setPostValue([]);
                    $this->_messageManager->addErrorMessage(__('Bad words accquired, please update'));
                }
            }

            $returnValue = $proceed();

            if ($matchFound) {
                if (isset($input['editRecordId'])) {
                    $resultPage = $subject->_resultJsonFactory->create();
                    return $resultPage->setData($input);
                } else {
                    $resultPage = $subject->_resultRedirectFactory->create();
                    return $resultPage->setPath('*/*/index');
                }
            }
            return $returnValue;
        }
    }
}