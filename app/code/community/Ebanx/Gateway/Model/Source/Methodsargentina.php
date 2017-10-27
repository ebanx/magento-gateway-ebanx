<?php

class Ebanx_Gateway_Model_Source_Methodsargentina
{
	const RAPIPAGO = 'ebanx_rapipago';
	const PAGOFACIL = 'ebanx_pagofacil';

	public function toOptionArray()
	{
		return array(
			array(
				'value' => self::RAPIPAGO,
				'label' => Mage::helper('ebanx')->__('Rapipago')
			),
			array(
				'value' => self::PAGOFACIL,
				'label' => Mage::helper('ebanx')->__('Pago Facil')
			),
		);
	}
}
