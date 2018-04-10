<?php

class Ebanx_Gateway_Block_Info_Debitcard extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Debitcard
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/debitcard.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
