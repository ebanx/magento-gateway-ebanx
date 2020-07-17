<?php

class Ebanx_Gateway_Block_Info_Debitcardmx extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Debitcardmx
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/debitcard_mx.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
