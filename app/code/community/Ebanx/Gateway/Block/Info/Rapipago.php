<?php

class Ebanx_Gateway_Block_Info_Rapipago extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Rapipago
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/rapipago.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
