<?php

class Ebanx_Gateway_Block_Form_Wallet extends Ebanx_Gateway_Block_Form_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Form_Wallet
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/form/wallet.phtml');
    }
}
