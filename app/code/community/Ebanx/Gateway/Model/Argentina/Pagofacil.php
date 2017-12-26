<?php

class Ebanx_Gateway_Model_Argentina_Pagofacil extends Ebanx_Gateway_Payment
{
	protected $_code = 'ebanx_pagofacil';

	protected $_formBlockType = 'ebanx/form_pagofacil';
	protected $_infoBlockType = 'ebanx/info_pagofacil';

	public function __construct()
	{
		parent::__construct();
		$this->gateway = $this->ebanx->pagofacil();
	}

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_argentina']));
	}
}
