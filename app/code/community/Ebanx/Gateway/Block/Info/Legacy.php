<?php

class Ebanx_Gateway_Block_Info_Legacy extends Ebanx_Gateway_Block_Info_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		
		if (!$this->isAdmin()) {
			return;
		}

		$this->setTemplate('ebanx/info/default.phtml');
	}
}
