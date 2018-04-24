<?php

class Ebanx_Gateway_Block_Info_Pagoefectivo extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Pagoefectivo
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/pagoefectivo.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
