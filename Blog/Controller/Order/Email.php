<?php
namespace Ecommage\Blog\Controller\Order;

use Magento\Sales\Model\Order;
use Ecommage\Blog\Helper\SendMail;

class Email extends \Magento\Framework\App\Action\Action
{

     protected $_orderFactory;

     protected $_helperSendMail;


     public function __construct(
          \Magento\Framework\App\Action\Context $context,
          Order $orderFactory,
          SendMail $helperSendMail
     ) {
        $this->_helperSendMail = $helperSendMail;
        $this->_orderFactory = $orderFactory;
          return parent::__construct($context);
     }

     public function execute()
    {
        $this->_helperSendMail->sendEmail();
        // $email = $this->getRequest()->getParam('email');
        // $order = $this->_orderFactory->create()->load('1'); // this is entity id
        // $order->setCustomerEmail($email);
        // if ($order) {
        //     try {
        //         $this->_objectManager->create('\Magento\Sales\Model\OrderNotifier')
        //             ->notify($order);
        //         $this->messageManager->addSuccess(__('You sent the order email.'));
        //     } catch (\Magento\Framework\Exception\LocalizedException $e) {
        //         $this->messageManager->addError($e->getMessage());
        //     } catch (\Exception $e) {
        //         $this->messageManager->addError(__('We can\'t send the email order right now.'));
        //         $this->_objectManager->create('Magento\Sales\Model\OrderNotifier')->critical($e);
        //     }
        // }
    }
}
?>