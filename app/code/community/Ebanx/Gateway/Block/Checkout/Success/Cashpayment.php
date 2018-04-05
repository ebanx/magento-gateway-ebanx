<?php

class Ebanx_Gateway_Block_Checkout_Success_Cashpayment extends Ebanx_Gateway_Block_Checkout_Success_Payment
{
	protected $_order;

	public function getEbanxDueDate($format = 'dd/MM')
	{
		$date = new Zend_Date($this->getPayment()->getEbanxDueDate());

		return $date->get($format);
	}

	public function getEbanxUrlPrint()
	{
		$hash = $this->getEbanxPaymentHash();
		return $this->helper->getVoucherUrlByHash($hash, 'print');
	}

	public function getEbanxPaymentHash()
	{
		return $this->getOrder()->getPayment()->getEbanxPaymentHash();
	}

	public function getEbanxUrlPdf()
	{
		$hash = $this->getEbanxPaymentHash();
		return $this->helper->getVoucherUrlByHash($hash, 'pdf');
	}

	public function getEbanxUrlBasic()
	{
		$hash = $this->getEbanxPaymentHash();
		return $this->helper->getVoucherUrlByHash($hash, 'basic');
	}

	public function getVoucherUrl()
	{
		return Mage::getUrl('ebanx/voucher', array(
			'hash' => $this->getEbanxPaymentHash()
		));
	}

	public function getEbanxUrlMobile()
	{
		$hash = $this->getEbanxPaymentHash();
		return $this->helper->getVoucherUrlByHash($hash, 'mobile');
	}

	protected function _construct()
	{
		parent::_construct();
	}
}
