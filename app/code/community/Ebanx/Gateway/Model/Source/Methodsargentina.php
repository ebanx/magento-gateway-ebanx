<?php

class Ebanx_Gateway_Model_Source_Methodsargentina
{
	const RAPIPAGO = 'ebanx_rapipago';
	const OTROS_CUPONES = 'ebanx_otros_cupones';

	public function toOptionArray()
	{
		return array(
			array(
				'value' => self::RAPIPAGO,
				'label' => Mage::helper('ebanx')->__('Rapipago')
			),
			array(
				'value' => self::OTROS_CUPONES,
				'label' => Mage::helper('ebanx')->__('Otros Cupones')
			),
		);
	}
}
