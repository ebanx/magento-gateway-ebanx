<?php

class Ebanx_Gateway_Block_Form_Boleto extends Ebanx_Gateway_Block_Form_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/form/boleto.phtml');
	}
}
