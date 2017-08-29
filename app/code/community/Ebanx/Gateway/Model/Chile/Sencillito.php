<?php

class Ebanx_Gateway_Model_Chile_Sencillito extends Ebanx_Gateway_Payment
{
	protected $_code = 'ebanx_sencillito';

	protected $_formBlockType = 'ebanx/form_sencillito';
	protected $_infoBlockType = 'ebanx/info_sencillito';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->sencillito();
	}

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_chile']));
	}
}
