<?php

class Ebanx_Gateway_Model_Source_Methodsmexico
{
	const CREDIT_CARD = 'ebanx_cc_mx';
	const DEBIT_CARD = 'ebanx_dc_mx';
	const OXXO = 'ebanx_oxxo';
	const SPEI = 'ebanx_spei';

	public function toOptionArray()
	{
		return array(
			array(
				'value' => self::CREDIT_CARD,
				'label' => Mage::helper('ebanx')->__('Credit Card')
			),
			array(
				'value' => self::DEBIT_CARD,
				'label' => Mage::helper('ebanx')->__('Debit Card')
			),
			array(
				'value' => self::OXXO,
				'label' => Mage::helper('ebanx')->__('OXXO')
			),
			array(
				'value' => self::SPEI,
				'label' => Mage::helper('ebanx')->__('SPEI')
			),
		);
	}
}
