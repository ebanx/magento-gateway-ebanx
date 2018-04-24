<?php

class Ebanx_Gateway_Block_Checkout_Success_Payment_Creditcardmx extends Ebanx_Gateway_Block_Checkout_Success_Creditcardpayment
{
    protected $currencyCode = 'MXN';

    /**
     * @return Ebanx_Gateway_Block_Checkout_Success_Payment_Creditcardmx
     */
    protected function _construct()
    {
        parent::_construct();
    }
}
