<?php

class Ebanx_Gateway_Model_Mexico_Oxxo extends Ebanx_Gateway_Payment
{
	protected $_code = 'ebanx_oxxo';

	protected $_formBlockType = 'ebanx/form_oxxo';
	protected $_infoBlockType = 'ebanx/info_oxxo';

	public function __construct()
	{
		parent::__construct();
		$this->gateway = $this->ebanx->oxxo();
	}

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_mexico']));
	}
}
