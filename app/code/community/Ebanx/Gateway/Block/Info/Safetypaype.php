<?php

class Ebanx_Gateway_Block_Info_Safetypaype extends Ebanx_Gateway_Block_Info_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/info/safetypaype.phtml');
		if ($this->isAdmin()) {
			$this->setTemplate('ebanx/info/default.phtml');
		}
	}
}
