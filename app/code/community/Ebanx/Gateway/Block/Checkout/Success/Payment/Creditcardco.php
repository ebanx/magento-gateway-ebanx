<?php

class Ebanx_Gateway_Block_Checkout_Success_Payment_Creditcardco extends Ebanx_Gateway_Block_Checkout_Success_Creditcardpayment
{
	protected $currencyCode = 'COP';

	protected function _construct()
	{
		parent::_construct();
	}
}
