<?php

abstract class Ebanx_Gateway_Model_Payment_Creditcard extends Ebanx_Gateway_Model_Payment
{
	protected $_canSaveCc = false;

	public function __construct()
	{
		parent::__construct();


		$this->ebanx = Mage::getSingleton('ebanx/api')->ebanxCreditCard();
		$this->gateway = $this->ebanx->creditCard();
	}

	public function getInstalmentTerms($grandTotal = null)
	{
		$amount = $grandTotal ?: $this->getTotal();
		return $this->gateway->getPaymentTermsForCountryAndValue($this->getCountry(), $amount);
	}

	public function getTotal()
	{
		$quote = $this->getInfoInstance()->getQuote();
		return $quote->getGrandTotal();
	}

	/**
	 * @return string
	 */
	abstract protected function getCountry();

	public function canUseForCountry($country)
	{
		return $this->helper->transformCountryCodeToName($country) === $this->getCountry()
			&& parent::canUseForCountry($country);
	}

	public function setupData()
	{
		parent::setupData();

		$this->data->setGatewayFields(Mage::app()->getRequest()->getPost('payment'));
		$this->data->setPaymentType('cc');
		$this->data->setInstalmentTerms(
			$this->gateway->getPaymentTermsForCountryAndValue(
				$this->helper->transformCountryCodeToName($this->data->getBillingAddress()->getCountry()),
				$this->data->getAmountTotal()
			)
		);
	}

	public function transformPaymentData()
	{
		$this->paymentData = $this->adapter->transformCard($this->data);
	}

	public function persistPayment()
	{
		parent::persistPayment();

		$gatewayFields = $this->data->getGatewayFields();
		$last4 = substr($gatewayFields['ebanx_masked_card_number'], -4);
		$instalments = array_key_exists('instalments', $gatewayFields) ? $gatewayFields['instalments'] : 1;
		$this->payment->setInstalments($instalments)
			->setCcLast4($last4)
			->setCcType($gatewayFields['ebanx_brand']);

		$this->persistCreditCardData();
	}

	private function persistCreditCardData()
	{
		$gatewayFields = $this->data->getGatewayFields();
		var_dump($gatewayFields);
		if (isset($gatewayFields['ebanx_save_credit_card']) && $gatewayFields['ebanx_save_credit_card']){
			exit('ENTROU');

			// get token
			// get brand
			// get masked number
		}
		exit('SAIU');

	}
}
