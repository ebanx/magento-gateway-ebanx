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
	}

	public function initialize($paymentAction, $stateObject)
	{
		$safetyPayType = Mage::app()->getRequest()->getPost()['ebanx_safetypay_type'];

		$this->gateway = $this->ebanx->safetyPay($safetyPayType);

		parent::initialize($paymentAction, $stateObject);
	}
}
