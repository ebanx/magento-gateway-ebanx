<?php

class Ebanx_Gateway_Model_Chile_Webpay extends Ebanx_Gateway_Payment
{
	protected $_code = 'ebanx_webpay';

	protected $_formBlockType = 'ebanx/form_webpay';
	protected $_infoBlockType = 'ebanx/info_webpay';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->webpay();
	}

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_chile']));
	}
}
