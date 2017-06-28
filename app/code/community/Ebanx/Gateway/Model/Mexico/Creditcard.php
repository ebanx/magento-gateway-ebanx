<?php
use Ebanx\Benjamin\Models\Country;

class Ebanx_Gateway_Model_Mexico_Creditcard extends Ebanx_Gateway_Model_Payment_Creditcard
{
	protected $_code = 'ebanx_cc_mx';

	protected $_formBlockType = 'ebanx/form_creditcard_mx';
	protected $_infoBlockType = 'ebanx/info_creditcardmx';

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_mexico']));
	}

	/**
	 * @return string
	 */
	protected function getCountry()
	{
		return Country::MEXICO;
	}
}
