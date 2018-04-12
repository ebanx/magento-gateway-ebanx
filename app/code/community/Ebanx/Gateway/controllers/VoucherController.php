<?php

class Ebanx_Gateway_VoucherController extends Mage_Core_Controller_Front_Action
{
    /**
     * @return void
     */
    public function indexAction()
    {
        $hash = $this->getRequest()->getParam('hash');
        $voucherHtml = Mage::getSingleton('ebanx/api')->ebanx()->getTicketHtml($hash);

        $this->getResponse()
             ->setHeader('Content-Type', 'text/html')
             ->setBody($voucherHtml);
    }
}
