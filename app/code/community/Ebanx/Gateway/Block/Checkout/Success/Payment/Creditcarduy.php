<?php

class Ebanx_Gateway_Block_Checkout_Success_Payment_Creditcarduy extends Ebanx_Gateway_Block_Checkout_Success_Creditcardpayment
{
    protected $currencyCode = 'UYU';

    /**
     * @return Ebanx_Gateway_Block_Checkout_Success_Payment_Creditcarduy
     */
    protected function _construct()
    {
        parent::_construct();
    }
}
