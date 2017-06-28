<?php

class Ebanx_Gateway_Block_Checkout_Success_Payment_Debitcard extends Ebanx_Gateway_Block_Checkout_Success_Creditcardpayment
{
	protected $currencyCode = 'MXN';

	protected function _construct()
	{
		parent::_construct();
	}
}
