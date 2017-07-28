<?php

abstract class Ebanx_Gateway_Block_Info_Abstract extends Mage_Payment_Block_Info
{
	public function getLocalAmount($currency, $formatted = true)
	{
		return Mage::helper('ebanx/amount')->getLocalAmount($currency, $formatted);
	}
}
