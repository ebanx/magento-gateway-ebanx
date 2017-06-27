<?php

class Ebanx_Gateway_Block_Form_Creditcard_Br extends Ebanx_Gateway_Block_Form_Creditcard
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
		return $hasInterests ? 'com juros' : '';
	}
}
