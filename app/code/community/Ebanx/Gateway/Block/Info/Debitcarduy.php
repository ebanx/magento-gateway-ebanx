<?php

class Ebanx_Gateway_Block_Info_Debitcarduy extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Debitcarduy
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/debitcard_uy.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
