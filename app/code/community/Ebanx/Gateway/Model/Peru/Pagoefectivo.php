<?php

class Ebanx_Gateway_Model_Peru_Pagoefectivo extends Ebanx_Gateway_Payment
{
	protected $_code = 'ebanx_pagoefectivo';

	protected $_formBlockType = 'ebanx/form_pagoefectivo';
	protected $_infoBlockType = 'ebanx/info_pagoefectivo';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->pagoefectivo();
	}

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_peru']));
	}
}
