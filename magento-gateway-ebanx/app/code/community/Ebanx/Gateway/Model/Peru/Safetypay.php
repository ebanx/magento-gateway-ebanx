<?php

class Ebanx_Gateway_Model_Peru_Safetypay extends Ebanx_Gateway_Model_Payment
{
	protected $gateway;

	protected $_code = 'ebanx_safetypay';

	protected $_formBlockType = 'ebanx/form_safetypay';
	protected $_infoBlockType = 'ebanx/info_safetypay';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->safetypay();
	}
}
