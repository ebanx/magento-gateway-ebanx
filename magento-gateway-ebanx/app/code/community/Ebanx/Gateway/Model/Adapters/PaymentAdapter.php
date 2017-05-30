<?php

require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';
use Ebanx\Benjamin\Models\Currency;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Models\Address;
use Ebanx\Benjamin\Models\Person;
use Ebanx\Benjamin\Models\Item;

class Ebanx_Gateway_Model_Adapters_PaymentAdapter
{
	/**
	 * @param Varien_Object $data
	 * @return Payment
	 */
	public function transform(Varien_Object $data)
	{
		return new Payment([
			'type' => $data->getEbanxMethod()
		]);
	}

	public function transformAddress($address)
	{
		return new Address([]);
	}
}