<?php

class Ebanx_Gateway_Block_Form_Creditcard_Ar extends Ebanx_Gateway_Block_Form_Creditcard
{
	public $country = 'ar';
	public $localCurrency = 'ARS';

	/**
	 * @return string
	 */
	protected function getTemplatePath()
	{
		return 'ebanx/form/creditcard_ar.phtml';
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
			'local-amount' => 'Total a pagar en Peso argentino: ',
			'card-number' => 'Número de la tarjeta',
			'duedate' => 'Fecha de expiración',
			'cvv' => 'Código de verificación',
			'save' => 'Salvar este cartão para compras futuras',
			'instalments' => 'Número de parcelas',
			'name' => 'Titular de la tarjeta',
		);
	}
}
