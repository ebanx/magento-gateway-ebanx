<?php

class Ebanx_Gateway_Block_Checkout_Success_Payment extends Mage_Checkout_Block_Onepage_Success
{
	protected $_order;

	public function getSuccessPaymentBlock()
	{

		return $this->getPayment()->getMethodInstance()->getCode();
	}

	public function getPayment()
	{
		return $this->getOrder()->getPayment();
	}

	public function getOrder()
	{
		if (is_null($this->_order)) {
			$lastOrderId = Mage::getSingleton('checkout/session')->getLastOrderId();
			$this->_order = Mage::getModel('sales/order')->load($lastOrderId);
		}

		return $this->_order;
	}

	protected function _construct()
	{
		parent::_construct();
	}
}
