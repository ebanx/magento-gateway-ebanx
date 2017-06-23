<?php

class Ebanx_Gateway_Block_Checkout_Success_Payment_Baloto extends Ebanx_Gateway_Block_Checkout_Success_CashPayment
{
	public function getEbanxUrl()
	{
		return parent::getEbanxUrl() . 'baloto/execute';
	}

	protected function _construct()
	{
		parent::_construct();
	}
}
