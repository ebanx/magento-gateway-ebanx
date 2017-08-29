<?php

class Ebanx_Gateway_Model_Brazil_Wallet extends Ebanx_Gateway_Payment
{
	protected $_code = 'ebanx_wallet';

	protected $_formBlockType = 'ebanx/form_wallet';
	protected $_infoBlockType = 'ebanx/info_wallet';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->ebanxAccount();
	}

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_brazil']));
	}
}
