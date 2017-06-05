<?php
class Ebanx_Gateway_Block_Checkout_Success_CashPayment extends Ebanx_Gateway_Block_Checkout_Success_Payment {
    protected $_order;

    protected function _construct() {
        parent::_construct();
    }

    public function getEbanxPaymentHash() {
        return $this->getOrder()->getPayment()->getEbanxPaymentHash();
    }
    
    public function getEbanxDueDate() {
        return Mage::helper('core')->formatDate($this->getOrder()->getPayment()->getEbanxDueDate(), Mage_Core_Model_Locale::FORMAT_TYPE_FULL, false);
    }

	public function getEbanxUrl() {
        return Mage::getSingleton('ebanx/api')->getEbanxUrl() . '?hash=' . self::getEbanxPaymentHash();
    }

	public function getEbanxUrlPdf() {
        return self::getEbanxUrl() . '&format=pdf';
    }

}
