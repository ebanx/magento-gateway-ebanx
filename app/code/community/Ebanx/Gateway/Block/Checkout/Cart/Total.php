<?php

class Ebanx_Gateway_Block_Checkout_Cart_Total extends Mage_Core_Block_Template
{
    /**
     * @return Ebanx_Gateway_Block_Checkout_Cart_Total
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/checkout/cart/total.phtml');
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getAmount() == $this->_getQuote()->getBaseGrandTotal()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        $amount = $this->_getQuote()->getBaseGrandTotal();

        return $amount;
    }

    /**
     * @return mixed
     */
    protected function _getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * @return mixed
     */
    protected function _getPayment()
    {
        return $this->_getQuote()->getPayment();
    }
}
