<?php

class Ebanx_Gateway_Model_Source_Methodschile
{
	const SENCILLITO = 'ebanx_sencillito';
	const SERVIPAG = 'ebanx_servipag';
	const WEBPAY = 'ebanx_webpay';
	const MULTICAJA = 'ebanx_multicaja';

	public function toOptionArray()
	{
		return array(
			array(
				'value' => self::SENCILLITO,
				'label' => Mage::helper('ebanx')->__('Sencillito')
			),
			array(
				'value' => self::SERVIPAG,
				'label' => Mage::helper('ebanx')->__('Servipag')
			),
			array(
				'value' => self::WEBPAY,
				'label' => Mage::helper('ebanx')->__('Webpay')
      ),
      array(
				'value' => self::MULTICAJA,
				'label' => Mage::helper('ebanx')->__('Multicaja')
			),
		);
	}
}
