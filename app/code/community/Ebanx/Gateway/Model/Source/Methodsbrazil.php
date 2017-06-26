<?php

class Ebanx_Gateway_Model_Source_Methodsbrazil
{
	const CREDIT_CARD = 'ebanx_cc_br';
	const BOLETO = 'ebanx_boleto';
	const TEF = 'ebanx_tef';
	const WALLET = 'ebanx_wallet';

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
