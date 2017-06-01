<?php

class Ebanx_Gateway_Model_Brazil_Boleto extends Ebanx_Gateway_Model_Payment
{
	protected $gateway;

	protected $_code = 'ebanx_boleto';

	protected $_formBlockType = 'ebanx/form_boleto';
	protected $_infoBlockType = 'ebanx/info_boleto';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->boleto();
	}
}