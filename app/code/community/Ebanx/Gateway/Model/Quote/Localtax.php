<?php

class Ebanx_Gateway_Model_Quote_Localtax extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	public function __construct()
	{
		$this->setCode( 'ebanx_local_tax' );
	}

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		$isBrazilLocalAmount = Mage::app()->getStore()->getCurrentCurrencyCode() === 'BRL';
		$quoteData = Mage::getModel('checkout/session')->getQuote()->getData();
		if ($isBrazilLocalAmount && array_key_exists('grand_total', $quoteData)) {
			$grandTotal = $quoteData['grand_total'];
			$localTaxAmount = $grandTotal * 1.0038 - $grandTotal;

			$address->setEbanxLocalTaxAmount($localTaxAmount / 2);
			$address->setGrandTotal($address->getGrandTotal() + $localTaxAmount / 2);
		}
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$amount = $address->getEbanxLocalTaxAmount();
		$title  = Mage::helper( 'ebanx' )->__( 'IOF' );
		if ( $amount != 0 ) {
			$address->addTotal( array(
				'code'  => $this->getCode(),
				'title' => $title,
				'value' => $amount,
			) );
		}
		return $this;
	}
}
