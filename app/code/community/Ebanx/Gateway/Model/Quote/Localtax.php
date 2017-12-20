<?php

use Ebanx\Benjamin\Models\Configs\Config;

class Ebanx_Gateway_Model_Quote_Localtax extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	public function __construct()
	{
		$this->setCode( 'ebanx_local_tax' );
	}

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		$isBrazilLocalAmount = Mage::app()->getStore()->getCurrentCurrencyCode() === 'BRL';
		if ($isBrazilLocalAmount && $address->getGrandTotal() > 0) {
			$grandTotal = $address->getGrandTotal();
			$localTaxAmount = $grandTotal * Config::IOF;

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
