<?php

class Ebanx_Gateway_Block_Info_Safetypayec extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Safetypayec
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/safetypayec.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
