<?php

class Ebanx_Gateway_Block_Checkout_Success_Payment_Baloto extends Ebanx_Gateway_Block_Checkout_Success_Cashpayment
{
    /**
     * @return string
     */
    public function getEbanxUrl()
    {
        return parent::getEbanxUrl() . 'baloto/execute';
    }

    /**
     * @return Ebanx_Gateway_Block_Checkout_Success_Cashpayment|void
     */
    protected function _construct()
    {
        parent::_construct();
    }
}
