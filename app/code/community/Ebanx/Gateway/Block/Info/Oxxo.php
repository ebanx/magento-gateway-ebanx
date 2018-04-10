<?php

class Ebanx_Gateway_Block_Info_Oxxo extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Oxxo
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/oxxo.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
