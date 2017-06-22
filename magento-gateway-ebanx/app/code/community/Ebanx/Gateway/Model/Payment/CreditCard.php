<?php

abstract class Ebanx_Gateway_Model_Payment_CreditCard extends Ebanx_Gateway_Model_Payment
{
	protected $_canSaveCc = false;

	/**
	 * @return string
	 */
	abstract protected function getCountry();

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->creditCard();
	}

	public function getInstalmentTerms()
	{
		$quote = $this->getInfoInstance()->getQuote();
		$amount = $quote->getGrandTotal();

		return $this->gateway->getPaymentTermsForCountryAndValue($this->getCountry(), $amount);
	}

	public function canUseForCountry($country)
	{
		return $this->helper->transformCountryCodeToName($country) === $this->getCountry()
			&& parent::canUseForCountry($country);
	}

	public function transformPaymentData()
	{
		$this->paymentData = $this->adapter->transformCard($this->data);
	}

	public function persistPayment()
	{
		parent::persistPayment();

		$params = Mage::app()->getRequest()->getParams();
		$paymentData = $params['payment'];
		$last4 = substr($paymentData['ebanx_masked_card_number'], -4);

		$this->payment
			->setCcLast4($last4)
			->setCcType($paymentData['ebanx_brand']);
	}
}
