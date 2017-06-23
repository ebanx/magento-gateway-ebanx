<?php

class Ebanx_Gateway_Block_Checkout_Success_Payment_CreditCardBR extends Ebanx_Gateway_Block_Checkout_Success_CreditCardPayment
{
	private $currencyCode = 'BRL';

	protected function _construct()
	{
		parent::_construct();
	}
}
