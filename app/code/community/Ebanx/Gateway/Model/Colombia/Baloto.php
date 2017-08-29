<?php

class Ebanx_Gateway_Model_Colombia_Baloto extends Ebanx_Gateway_Payment
{
	protected $_code = 'ebanx_baloto';

	protected $_formBlockType = 'ebanx/form_baloto';
	protected $_infoBlockType = 'ebanx/info_baloto';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->baloto();
	}

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_colombia']));
	}
}
