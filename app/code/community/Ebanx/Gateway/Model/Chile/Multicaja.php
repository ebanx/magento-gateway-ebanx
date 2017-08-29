<?php

class Ebanx_Gateway_Model_Chile_Multicaja extends Ebanx_Gateway_Payment
{
	protected $_code = 'ebanx_multicaja';

	protected $_formBlockType = 'ebanx/form_multicaja';
	protected $_infoBlockType = 'ebanx/info_multicaja';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->multicaja();
	}

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_chile']));
	}
}
