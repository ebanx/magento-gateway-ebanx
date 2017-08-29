<?php

class Ebanx_Gateway_Model_Brazil_Boleto extends Ebanx_Gateway_Payment
{
	protected $_code = 'ebanx_boleto';

	protected $_formBlockType = 'ebanx/form_boleto';
	protected $_infoBlockType = 'ebanx/info_boleto';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->boleto();
	}

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_brazil']));
	}

	public function persistPayment()
	{
		parent::persistPayment();
		$this->payment->setEbanxBarCode($this->result['payment']['boleto_barcode']);
	}
}
