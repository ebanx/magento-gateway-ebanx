<?php

class Ebanx_Gateway_Model_Chile_Servipag extends Ebanx_Gateway_Model_Payment
{
	protected $gateway;

	protected $_code = 'ebanx_servipag';

	protected $_formBlockType = 'ebanx/form_servipag';
	protected $_infoBlockType = 'ebanx/info_servipag';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->servipag();
	}
}
