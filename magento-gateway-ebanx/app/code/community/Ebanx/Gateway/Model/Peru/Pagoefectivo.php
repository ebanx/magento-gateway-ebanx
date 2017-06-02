<?php

class Ebanx_Gateway_Model_Peru_Pagoefectivo extends Ebanx_Gateway_Model_Payment
{
	protected $gateway;

	protected $_code = 'ebanx_pagoefectivo';

	protected $_formBlockType = 'ebanx/form_pagoefectivo';
	protected $_infoBlockType = 'ebanx/info_pagoefectivo';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->pagoefectivo();
	}
}
