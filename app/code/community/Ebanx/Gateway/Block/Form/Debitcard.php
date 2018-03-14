<?php

class Ebanx_Gateway_Block_Form_Debitcard extends Mage_Payment_Block_Form_Cc
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/form/debitcard.phtml');
	}

	private function formatPriceWithLocalCurrency($currency, $price)
	{
		return Mage::app()->getLocale()->currency($currency)->toCurrency($price);
	}

	public function getLocalAmount($currency, $formatted = true)
	{
		$amount = round(Mage::helper('ebanx')->getLocalAmountWithTax($currency, $this->getMethod()->getTotal()), 2);

		return $formatted ? $this->formatPriceWithLocalCurrency($currency, $amount) : $amount;
	}

	public function getLocalAmountWithoutTax($currency, $formatted = true)
	{
		$amount = round(Mage::helper('ebanx')->getLocalAmountWithoutTax($currency, $this->getMethod()->getTotal()), 2);

		return $formatted ? $this->formatPriceWithLocalCurrency($currency, $amount) : $amount;
	}
}
