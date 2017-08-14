<?php

class Ebanx_Gateway_Block_Form_Baloto extends Ebanx_Gateway_Block_Form_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/form/baloto.phtml');
	}
}
