<?php

class Ebanx_Gateway_Helper_Amount extends Ebanx_Gateway_Helper_Data
{
	private function getQuoteData($key = null)
	{
		$quote = Mage::getModel('checkout/session')->getQuote();
		$quoteData = $quote->getData();

		if (is_string($key)) {
			return $quoteData[$key];
		}

		return $quoteData;
	}

	public function getGrandTotal()
	{
		return $this->getQuoteData('grand_total');
	}

	public function formatPriceWithLocalCurrency($currency, $price)
	{
		return Mage::app()->getLocale()->currency($currency)->toCurrency($price);
	}

	public function getLocalAmount($currency, $formatted = true)
	{
		$amount = round(Mage::helper('ebanx')->getLocalAmountWithTax($currency, $this->getGrandTotal()), 2);

		return $formatted ? $this->formatPriceWithLocalCurrency($currency, $amount) : $amount;
	}
}
