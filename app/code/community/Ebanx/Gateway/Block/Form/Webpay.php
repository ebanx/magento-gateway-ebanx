<?php

class Ebanx_Gateway_Block_Form_Webpay extends Ebanx_Gateway_Block_Form_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Form_Webpay
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/form/webpay.phtml');
    }
}
