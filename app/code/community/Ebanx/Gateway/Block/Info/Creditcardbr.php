<?php

class Ebanx_Gateway_Block_Info_Creditcardbr extends Ebanx_Gateway_Block_Info_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/info/creditcard_br.phtml');
	}
}
