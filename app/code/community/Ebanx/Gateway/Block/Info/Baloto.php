<?php

class Ebanx_Gateway_Block_Info_Baloto extends Mage_Payment_Block_Info
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/info/baloto.phtml');
	}
}
