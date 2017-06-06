<?php

class Ebanx_Gateway_Model_Source_MethodsBrazil
{
	const CREDIT_CARD = 'credit_card';
	const BOLETO = 'boleto';
	const TEF = 'tef';
	const WALLET = 'wallet';

	public function toOptionArray()
	{
		return array(
			array(
				'value' => self::CREDIT_CARD,
				'label' => Mage::helper('ebanx')->__('Credit Card')
			),
			array(
				'value' => self::BOLETO,
				'label' => Mage::helper('ebanx')->__('Boleto EBANX')
			),
			array(
				'value' => self::TEF,
				'label' => Mage::helper('ebanx')->__('Online Banking (TEF)')
			),
			array(
				'value' => self::WALLET,
				'label' => Mage::helper('ebanx')->__('EBANX Wallet')
			),
		);
	}
}
