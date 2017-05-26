<?php
class Ebanx_Gateway_Block_Checkout_Success_Payment_Boleto extends Ebanx_Gateway_Block_Checkout_Success_Payment {
    protected function _construct() {
        parent::_construct();
    }
    
    public function getEbanxPaymentHash() {
        return $this->getOrder()->getPayment()->getEbanxPaymentHash();
    }
    
    public function getEbanxDueDate() {
        return Mage::helper('core')->formatDate($this->getOrder()->getPayment()->getEbanxDueDate(), Mage_Core_Model_Locale::FORMAT_TYPE_FULL, false); // Sunday, May 15, 2016 3:05:15 PM America/New_York
    }
}
