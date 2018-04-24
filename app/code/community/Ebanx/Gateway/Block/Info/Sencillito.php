<?php

class Ebanx_Gateway_Block_Info_Sencillito extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Sencillito
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/sencillito.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
