<?php

class Ebanx_Gateway_Model_Source_Methodsargentina
{
	const RAPIPAGO = 'ebanx_rapipago';

	public function toOptionArray()
	{
		return array(
			array(
				'value' => self::RAPIPAGO,
				'label' => Mage::helper('ebanx')->__('Rapipago')
			),
		);
	}
}
