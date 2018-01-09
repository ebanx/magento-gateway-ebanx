<?php

use Ebanx\Benjamin\Models\Country;

class Ebanx_Gateway_Model_Peru_Safetypay extends Ebanx_Gateway_Model_Payment_Safetypay
{
	protected $_code = 'ebanx_safetypay';

	protected $_formBlockType = 'ebanx/form_safetypay_pe';
	protected $_infoBlockType = 'ebanx/info_safetypaype';

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_peru']));
	}

	/**
	 * @return string
	 */
	protected function getCountry()
	{
		return Country::PERU;
	}
}
