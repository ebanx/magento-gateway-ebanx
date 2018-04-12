<?php

class Ebanx_Gateway_Block_Info_Creditcardmx extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Creditcardmx
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/creditcard_mx.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
