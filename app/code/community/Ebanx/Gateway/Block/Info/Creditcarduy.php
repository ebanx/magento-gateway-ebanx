<?php

class Ebanx_Gateway_Block_Info_Creditcarduy extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Creditcarduy
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/creditcard_uy.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
