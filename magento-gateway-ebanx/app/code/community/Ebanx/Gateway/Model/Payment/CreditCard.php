<?php

abstract class Ebanx_Gateway_Model_Payment_CreditCard extends Ebanx_Gateway_Model_Payment
{
	protected $_canSaveCc     			= false;

	public function transformPaymentData()
	{
		$this->paymentData = $this->adapter->transformCard($this->data);
	}
}
