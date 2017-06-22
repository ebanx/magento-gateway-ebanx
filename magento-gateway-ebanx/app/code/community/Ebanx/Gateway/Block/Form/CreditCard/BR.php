<?php

class Ebanx_Gateway_Block_Form_CreditCard_BR extends Ebanx_Gateway_Block_Form_CreditCard
{
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
		return $hasInterests ? 'com juros' : 'sem juros';
	}
}
