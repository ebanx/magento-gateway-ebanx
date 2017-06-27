<?php

class Ebanx_Gateway_Model_Quote_Total extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	public function __construct()
	{
		$this->setCode('ebanx_interest');
	}

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		$payment = $address->getQuote()->getPayment();

		if (!$payment->hasMethodInstance() || Mage::app()->getRequest()->getActionName() !== 'savePayment') {
			return $this;
		}

		$isCardPayment = substr($payment->getMethodInstance()->getCode(), 0, 8) === 'ebanx_cc';
		if (!$isCardPayment) {
			return $this;
		}

		$paymentInstance = $payment->getMethodInstance();

		$gatewayFields = Mage::app()->getRequest()->getPost('payment');
		$instalments = $gatewayFields['instalments'];
		$grandTotal = $gatewayFields['grand_total'];
		$instalmentTerms = $paymentInstance->getInstalmentTerms($grandTotal);

		$instalmentAmount = $instalmentTerms[$instalments - 1]->baseAmount;
		$interestAmount = ($instalmentAmount * $instalments) - $grandTotal;

		if ($interestAmount > 0) {
			$address->setEbanxInterestAmount($interestAmount / 2);
			$address->setGrandTotal(($address->getGrandTotal() + $interestAmount) / 2);
		}

		return $this;
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$amount = $address->getEbanxInterestAmount();
		$title = Mage::helper('ebanx')->__('Interest Amount');
		if ($amount != 0) {
			$address->addTotal(array(
				'code' => $this->getCode(),
				'title' => $title,
				'value' => $amount,
			));
		}
		return $this;
	}
}
