<?php
class Ebanx_Gateway_Block_Checkout_Success_Payment_Boleto extends Ebanx_Gateway_Block_Checkout_Success_Payment {
    protected function _construct() {
        parent::_construct();
    }
    
    public function getEbanxPaymentHash() {
        return $this->getOrder()->getPayment()->getEbanxPaymentHash();
    }
}
