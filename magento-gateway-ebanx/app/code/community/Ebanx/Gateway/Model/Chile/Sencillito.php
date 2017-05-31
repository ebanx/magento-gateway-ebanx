<?php

class Ebanx_Gateway_Model_Chile_Sencillito extends Ebanx_Gateway_Model_Payment
{
	protected $gateway;

	protected $_code = 'ebanx_sencillito';

	protected $_formBlockType = 'ebanx/form_sencillito';
	protected $_infoBlockType = 'ebanx/info_sencillito';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->sencillito();
	}


	public function initialize($paymentAction, $stateObject)
	{
		parent::initialize($paymentAction, $stateObject);

		$res = $this->gateway->create($this->adapter->transform($this->data));

		Mage::log($res, null, 'ebanx-sencillito.log', true);
		
		if ($res['status'] !== 'SUCCESS') {
			Mage::throwException($res['status_message']);
		}

		if (!empty($res['redirect_url'])) {
			self::$redirect_url = $res['redirect_url'];
		}
	}

	public function getOrderPlaceRedirectUrl()
	{
		return self::$redirect_url;
	}
}
