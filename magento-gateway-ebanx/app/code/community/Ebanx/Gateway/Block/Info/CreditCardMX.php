<?php

class Ebanx_Gateway_Block_Info_CreditCardMX extends Mage_Payment_Block_Info
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/info/creditcard_mx.phtml');
	}
}
