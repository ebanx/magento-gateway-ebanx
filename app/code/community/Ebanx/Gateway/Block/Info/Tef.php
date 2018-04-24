<?php

class Ebanx_Gateway_Block_Info_Tef extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Tef
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/tef.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
