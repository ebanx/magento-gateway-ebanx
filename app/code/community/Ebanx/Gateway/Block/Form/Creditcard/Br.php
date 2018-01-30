<?php

class Ebanx_Gateway_Block_Form_Creditcard_Br extends Ebanx_Gateway_Block_Form_Creditcard
{
	public $country = 'br';
	public $localCurrency = 'BRL';

	/**
	 * @return string
	 */
	protected function getTemplatePath()
	{
		return 'ebanx/form/creditcard_br.phtml';
	}

	/**
	 * @param bool $hasInterests
	 * @return string
	 */
	protected function getInterestMessage($hasInterests)
	{
		return $hasInterests ? 'com juros' : '';
	}

	public function getText()
	{
		return array(
			'method-desc' => 'Pagar com Cartão de Crédito.',
			'newcard' => 'Novo cartão',
			'local-amount' => $this->getLocalAmountText(),
			'card-number' => 'Número do Cartão',
			'duedate' => 'Data de validade',
			'cvv' => 'Código de segurança',
			'save' => 'Salvar este cartão para compras futuras',
			'instalments' => 'Número de parcelas',
			'name' => '',
		);
	}

	private function getLocalAmountText() {
		return Mage::getStoreConfig('payment/ebanx_settings/iof_local_amount')
			? 'Total a pagar com IOF (0.38%): '
			: 'Total a pagar: ';
	}
}
