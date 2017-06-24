<?php

class Ebanx_Gateway_Block_Checkout_Cart_Total extends Mage_Core_Block_Template
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/checkout/cart/total.phtml');
	}

	protected function _toHtml()
	{
		if ($this->getAmount() == $this->_getQuote()->getBaseGrandTotal()) {
			return '';
		}
		return parent::_toHtml();
	}

	public function getAmount()
	{
		$amount = $this->_getQuote()->getBaseGrandTotal();
		$payment = $this->_getPayment();
		if ($payment->getMethod() == 'ebanx_cc_br') {
//            TODO: Get instalments
//            TODO: Get interest rate
//            TODO: Calc amount
		}

		return $amount;
	}

	protected function _getQuote()
	{
		return Mage::getSingleton('checkout/session')->getQuote();
	}

	protected function _getPayment()
	{
		return $this->_getQuote()->getPayment();
	}
}
