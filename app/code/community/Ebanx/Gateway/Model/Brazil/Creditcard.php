<?php
use Ebanx\Benjamin\Models\Country;

class Ebanx_Gateway_Model_Brazil_Creditcard extends Ebanx_Gateway_Model_Payment_Creditcard
{
	protected $_code = 'ebanx_cc_br';

	protected $_formBlockType = 'ebanx/form_creditcard_br';
	protected $_infoBlockType = 'ebanx/info_creditcardbr';

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_brazil']));
	}

	/**
	 * @return string
	 */
	protected function getCountry()
	{
		return Country::BRAZIL;
	}
}
