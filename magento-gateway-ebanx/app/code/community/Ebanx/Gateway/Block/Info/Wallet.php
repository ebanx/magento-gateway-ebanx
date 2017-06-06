<?php

class Ebanx_Gateway_Block_Info_Wallet extends Mage_Payment_Block_Info
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/info/wallet.phtml');
	}
}
