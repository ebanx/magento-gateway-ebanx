<?php

class Ebanx_Gateway_Block_Info_Multicaja extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Multicaja
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/multicaja.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
