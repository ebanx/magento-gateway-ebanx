<?php

class Ebanx_Gateway_Block_Checkout_Success_Payment_Creditcardbr extends Ebanx_Gateway_Block_Checkout_Success_Creditcardpayment
{
	private $currencyCode = 'BRL';

	protected function _construct()
	{
		parent::_construct();
	}
}
