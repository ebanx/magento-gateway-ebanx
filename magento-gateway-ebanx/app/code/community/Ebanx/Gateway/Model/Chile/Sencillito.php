<?php

class Ebanx_Gateway_Model_Chile_Sencillito extends Ebanx_Gateway_Model_Payment
{
	protected $gateway;

	protected $_code = 'ebanx_sencillito';

	protected $_formBlockType = 'ebanx/form_sencillito';
	protected $_infoBlockType = 'ebanx/info_sencillito';

	public function __construct() {
		parent::__construct();

		$this->gateway = $this->ebanx->sencillito();
	}

	public function initialize($paymentAction, $stateObject)
	{
		parent::initialize($paymentAction, $stateObject);

		$res = $this->gateway->create($adapter->transform($this->data));

		Mage::log($data, null, 'ebanx-sencillito.log', true);
	}
	
}
