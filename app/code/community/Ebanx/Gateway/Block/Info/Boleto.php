<?php

class Ebanx_Gateway_Block_Info_Boleto extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Boleto
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/boleto.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
