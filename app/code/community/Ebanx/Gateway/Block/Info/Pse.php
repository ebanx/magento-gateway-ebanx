<?php

class Ebanx_Gateway_Block_Info_Pse extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Pse
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/pse.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
