<?php

class Ebanx_Gateway_Model_Quote_Localtax extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	public function __construct()
	{
		$this->setCode( 'ebanx_local_tax' );
	}

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		if ($address->getAddressType() !== Mage_Sales_Model_Quote_Address::TYPE_BILLING) {
			return;
		}

		$isBrazilLocalAmount = Mage::app()->getStore()->getCurrentCurrencyCode() === 'BRL';
		$grandTotal = Mage::getModel('checkout/session')->getQuote()->getGrandTotal();
//		$grandTotal = $address->getGrandTotal();
//		var_dump($grandTotal);
		if ($isBrazilLocalAmount && $grandTotal > 0) {
			$localTaxAmount = $grandTotal * 0.0038;

			$address->setEbanxLocalTaxAmount($localTaxAmount);
			$address->setGrandTotal($address->getGrandTotal() + $localTaxAmount);
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
