<?php
use Ebanx\Benjamin\Models\Country;

class Ebanx_Gateway_Model_Colombia_Creditcard extends Ebanx_Gateway_Model_Payment_Creditcard
{
	protected $_code = 'ebanx_cc_co';

	protected $_formBlockType = 'ebanx/form_creditcard_co';
	protected $_infoBlockType = 'ebanx/info_creditcardco';

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_colombia']));
	}

	/**
	 * @return string
	 */
	protected function getCountry()
	{
		return Country::COLOMBIA;
	}
}
