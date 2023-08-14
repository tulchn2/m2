<?php
 
namespace Ecommage\Blog\Helper;
 
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
 
class SendMail extends AbstractHelper
{
    const EMAIL_TEMPLATE = 'email_section/sendmail/email_template';
    protected $transportBuilder;
    protected $storeManager;
    protected $inlineTranslation;
 
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state
    )
    {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
        parent::__construct($context);
    }
 
    public function sendEmail()
    {
        // this is an example and you can change template id,fromEmail,toEmail,etc as per your need.
        $templateId = '1'; // template tessssst id 
        $fromEmail = 'tulchn2@gmail.com';  // sender Email id
        $fromName = 'Admin';             // sender Name
        $toEmail = 'tkgamehn2@gmail.com'; // receiver email id
 
        try {
            $storeId = $this->storeManager->getStore()->getId();
            // template variables pass here
            $templateVars = [
                'message_1' => 'CUSTOM MESSAGE STR 1',
                'message_2' => 'custom message str 2',
                'msg' => 'test',
                'test' => 'test1',
                'store' => $storeId,
            ];
            $sender = $this->scopeConfig->getValue(
                'email_section/sendmail/sender',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $templateId = $this->scopeConfig->getValue(
                self::EMAIL_TEMPLATE,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );

            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend();
 
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                // ->setFrom($from)
                ->setFromByScope($sender)
                ->addTo($toEmail)
                ->setReplyTo('replyto@email.com')
                ->addBcc('bcc@email.com')
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
    }
}
