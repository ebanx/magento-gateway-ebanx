<?php

class Ebanx_Gateway_Block_Form_Debitcard extends Mage_Payment_Block_Form_Cc
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/form/debitcard.phtml');
	}
}
