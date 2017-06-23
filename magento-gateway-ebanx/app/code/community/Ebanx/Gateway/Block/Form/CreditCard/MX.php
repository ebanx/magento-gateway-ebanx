<?php

class Ebanx_Gateway_Block_Form_CreditCard_MX extends Ebanx_Gateway_Block_Form_CreditCard
{
	/**
	 * @return string
	 */
	protected function getTemplatePath()
	{
		return 'ebanx/form/creditcard_mx.phtml';
	}

	/**
	 * @param bool $hasInterests
	 * @return string
	 */
	protected function getInterestMessage($hasInterests)
	{
		return $hasInterests ? 'con intereses' : 'sin interés';
	}
}
