<?php

class Ebanx_Gateway_Block_Info_Baloto extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Baloto
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/baloto.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
