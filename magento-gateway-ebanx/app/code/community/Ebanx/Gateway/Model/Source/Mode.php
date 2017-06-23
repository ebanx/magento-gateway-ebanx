<?php

class Ebanx_Gateway_Model_Source_Mode
{
	const SANDBOX = 'sandbox';
	const LIVE = 'live';

	public function toOptionArray()
	{
		return array(
			array(
				'value' => self::SANDBOX,
				'label' => Mage::helper('ebanx')->__('Sandbox')
			),
			array(
				'value' => self::LIVE,
				'label' => Mage::helper('ebanx')->__('Live')
			),
		);
	}
}
