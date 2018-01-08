<?php

class Ebanx_Gateway_Model_Source_Methodsecuador
{
	const SAFETYPAY = 'ebanx_safetypay';

	public function toOptionArray()
	{
		return array(
			array(
				'value' => self::SAFETYPAY,
				'label' => Mage::helper('ebanx')->__('SafetyPay')
			),
		);
	}
}
