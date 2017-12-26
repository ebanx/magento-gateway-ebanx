<?php

class Ebanx_Gateway_Block_Form_Otroscupones extends Ebanx_Gateway_Block_Form_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/form/otroscupones.phtml');
	}
}
