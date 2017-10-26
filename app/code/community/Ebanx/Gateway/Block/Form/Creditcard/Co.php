<?php

class Ebanx_Gateway_Block_Form_Creditcard_Co extends Ebanx_Gateway_Block_Form_Creditcard
{
	public $country = 'co';
	public $localCurrency = 'COP';

	/**
	 * @return string
	 */
	protected function getTemplatePath()
	{
		return 'ebanx/form/creditcard_co.phtml';
	}

	/**
	 * @param bool $hasInterests
	 * @return string
	 */
	protected function getInterestMessage($hasInterests)
	{
		return $hasInterests ? 'con intereses' : '';
	}

	public function getText()
	{
		return array(
			'method-desc' => 'Pagar con Tarjeta de Crédito.',
			'newcard' => 'Nueva tarjeta',
			'local-amount' => 'Total a pagar en Peso colombiano: ',
			'card-number' => 'Número de la tarjeta',
			'duedate' => 'Fecha de expiración',
			'cvv' => 'Código de verificación',
			'save' => 'Salvar este cartão para compras futuras',
			'instalments' => 'Número de parcelas',
			'name' => 'Titular de la tarjeta',
		);
	}
}
