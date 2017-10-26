<?php

class Ebanx_Gateway_Model_Source_Methodscolombia
{
	const EFT = 'ebanx_pse';
	const BALOTO = 'ebanx_baloto';
	const CREDIT_CARD = 'ebanx_cc_co';

	public function toOptionArray()
	{
		return array(
			array(
				'value' => self::EFT,
				'label' => Mage::helper('ebanx')->__('PSE - Pago Seguros en Líne (EFT)')
			),
			array(
				'value' => self::BALOTO,
				'label' => Mage::helper('ebanx')->__('Baloto')
			),
			array(
				'value' => self::CREDIT_CARD,
				'label' => Mage::helper('ebanx')->__('Credit Card')
			),
		);
	}
}
