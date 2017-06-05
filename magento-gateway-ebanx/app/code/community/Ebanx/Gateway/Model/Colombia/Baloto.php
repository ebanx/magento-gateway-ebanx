<?php

class Ebanx_Gateway_Model_Colombia_Baloto extends Ebanx_Gateway_Model_Payment
{
	protected $gateway;

	protected $_code = 'ebanx_baloto';

	protected $_formBlockType = 'ebanx/form_baloto';
	protected $_infoBlockType = 'ebanx/info_baloto';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->baloto();
	}

	public function persistPayment()
	{
		parent::persistPayment();
	}
}