<?php

class Ebanx_Gateway_Block_Checkout_Success_CashPayment extends Ebanx_Gateway_Block_Checkout_Success_Payment
{
	protected $_order;

	protected function _construct()
	{
		parent::_construct();
	}

	public function getEbanxDueDate()
	{
		return Mage::helper('core')->formatDate($this->getOrder()->getPayment()->getEbanxDueDate(), Mage_Core_Model_Locale::FORMAT_TYPE_FULL, false);
	}

	public function getEbanxUrl()
	{
		return $this->helper->getEbanxUrl();
	}

	public function getEbanxUrlPrint($type)
	{
		return $this->getEbanxUrlIframe($type) . '&format=print';
	}

	public function getEbanxUrlPdf($type)
	{
		return $this->getEbanxUrlIframe($type) . '&format=pdf';
	}

	public function getEbanxUrlBasic($type)
	{
		return $this->getEbanxUrlIframe($type) . '&format=basic';
	}

	public function getEbanxUrlIframe($type = '')
	{
		return $this->getEbanxUrl() . $type . '?hash=' . $this->getEbanxPaymentHash();
	}

	public function getEbanxPaymentHash()
	{
		return $this->getOrder()->getPayment()->getEbanxPaymentHash();
	}

	public function getVoucherUrl($type)
	{
		return Mage::getUrl('ebanx/voucher', [
			'hash' => $this->getEbanxPaymentHash(),
			'type' => $type
		]);
	}
}
