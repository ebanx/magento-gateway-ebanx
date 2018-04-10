<?php

class Ebanx_Gateway_Block_Info_Webpay extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Webpay
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/webpay.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
