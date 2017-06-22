<?php

class Ebanx_Gateway_Block_Checkout_Success_Payment_Baloto extends Ebanx_Gateway_Block_Checkout_Success_CashPayment
{
	protected function _construct()
	{
		parent::_construct();
	}

	public function getEbanxUrl()
	{
		return parent::getEbanxUrl() . 'baloto/execute';
	}
}
