<?php

class Ebanx_Gateway_Block_Info_Baloto extends Ebanx_Gateway_Block_Info_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/info/baloto.phtml');
		if ($this->isAdmin()) {
			$this->setTemplate('ebanx/info/default.phtml');
		}
	}
}
