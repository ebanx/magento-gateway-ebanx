<?php

abstract class Ebanx_Gateway_Model_Payment_Safetypay extends Ebanx_Gateway_Payment
{
	/**
	 * @return string
	 */
	abstract protected function getCountry();

	public function __construct() {
		parent::__construct();

		$this->gateway = $this->ebanx->safetyPayCash();
	}

	public function initialize( $paymentAction, $stateObject ) {
		$safetyPayType = Mage::app()->getRequest()->getPost()['ebanx_safetypay_type'];

		$this->gateway = $this->ebanx->{'safetyPay' . $safetyPayType}();

		parent::initialize( $paymentAction, $stateObject );
	}

	public function canUseForCountry($country)
	{
		return $this->helper->transformCountryCodeToName($country) === $this->getCountry()
			   && parent::canUseForCountry($country);
	}
}
