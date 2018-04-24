<?php

class Ebanx_Gateway_Block_Checkout_Success_Payment_Creditcardar extends Ebanx_Gateway_Block_Checkout_Success_Creditcardpayment
{
    protected $currencyCode = 'ARS';

    /**
     * @return Ebanx_Gateway_Block_Checkout_Success_Payment_Creditcardar
     */
    protected function _construct()
    {
        parent::_construct();
    }
}
