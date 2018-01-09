<?php

use Ebanx\Benjamin\Models\Country;

class Ebanx_Gateway_Model_Ecuador_Safetypay extends Ebanx_Gateway_Model_Payment_Safetypay
{
	protected $_code = 'ebanx_safetypay_ec';

	protected $_formBlockType = 'ebanx/form_safetypay_ec';
	protected $_infoBlockType = 'ebanx/info_safetypayec';

	public function isAvailable($quote = null)
	{
		return Ebanx_Gateway_Payment::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_ecuador']));
	}

	/**
	 * @return string
	 */
	protected function getCountry()
	{
		return Country::ECUADOR;
	}
}
