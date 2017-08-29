<?php

class Ebanx_Gateway_Model_Chile_Servipag extends Ebanx_Gateway_Payment
{
	protected $_code = 'ebanx_servipag';

	protected $_formBlockType = 'ebanx/form_servipag';
	protected $_infoBlockType = 'ebanx/info_servipag';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->servipag();
	}

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_chile']));
	}
}
