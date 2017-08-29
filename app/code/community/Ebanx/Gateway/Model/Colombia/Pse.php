<?php

class Ebanx_Gateway_Model_Colombia_Pse extends Ebanx_Gateway_Payment
{
	protected $_code = 'ebanx_pse';

	protected $_formBlockType = 'ebanx/form_pse';
	protected $_infoBlockType = 'ebanx/info_pse';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->eft();
	}

	public function transformPaymentData()
	{
		parent::transformPaymentData();

		$bankCode = Mage::app()->getRequest()->getPost('ebanx_pse_bank');

		$this->paymentData->bankCode = $bankCode;
	}

	public function isAvailable($quote = null)
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_colombia']));
	}
}
