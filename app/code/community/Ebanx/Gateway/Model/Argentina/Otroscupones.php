<?php

class Ebanx_Gateway_Model_Argentina_Otroscupones extends Ebanx_Gateway_Payment
{
	protected $_code = 'ebanx_otroscupones';

	protected $_formBlockType = 'ebanx/form_otroscupones';
	protected $_infoBlockType = 'ebanx/info_otroscupones';

	public function __construct()
	{
		parent::__construct();
		$this->gateway = $this->ebanx->otrosCupones();
	}

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_argentina']));
	}
}
