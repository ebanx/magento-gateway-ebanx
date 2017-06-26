<?php

class Ebanx_Gateway_Model_Source_MethodsColombia
{
	const EFT = 'ebanx_pse';
	const BALOTO = 'ebanx_baloto';

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
		);
	}
}
