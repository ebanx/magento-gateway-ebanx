<?php
use Ebanx\Benjamin\Models\Country;

class Ebanx_Gateway_Model_Argentina_Creditcard extends Ebanx_Gateway_Model_Payment_Creditcard
{
	protected $_code = 'ebanx_cc_ar';

	protected $_formBlockType = 'ebanx/form_creditcard_ar';
	protected $_infoBlockType = 'ebanx/info_creditcardar';

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_argentina']));
	}

	/**
	 * @return string
	 */
	protected function getCountry()
	{
		return Country::ARGENTINA;
	}
}
