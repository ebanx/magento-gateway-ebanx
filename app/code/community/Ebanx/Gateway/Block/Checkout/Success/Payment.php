<?php

class Ebanx_Gateway_Block_Checkout_Success_Payment extends Mage_Checkout_Block_Onepage_Success
{
    protected $_order;
    protected $helper;

    /**
     * @return Ebanx_Gateway_Block_Checkout_Success_Payment
     */
    protected function _construct()
    {
        parent::_construct();

        $this->helper = Mage::helper('ebanx/order');
    }

    /**
     * @return string
     */
    public function getSuccessPaymentBlock()
    {
        return $this->getPayment()->getMethodInstance()->getCode();
    }

    /**
     * @return Mage_Sales_Model_Order_Payment
     */
    public function getPayment()
    {
        return $this->getOrder()->getPayment();
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->_order)) {
            $lastOrderId = Mage::getSingleton('checkout/session')->getLastOrderId();
            $this->_order = Mage::getModel('sales/order')->load($lastOrderId);
        }

        return $this->_order;
    }

    /**
     * @return array
     */
    public function getCustomer()
    {
        $customerId = $this->getOrder()->getCustomerId();

        return array_merge(
            $this->helper->getCustomerData(),
            Mage::getModel('customer/customer')->load($customerId)->getData()
        );
    }

    /**
     * @param string $currency Currency type
     * @param float  $price    Amount
     *
     * @return string
     */
    public function formatPriceWithLocalCurrency($currency, $price)
    {
        return Mage::app()->getLocale()->currency($currency)->toCurrency($price);
    }
}
