<?php
class Ebanx_Gateway_Model_Brazil_CreditCard extends Ebanx_Gateway_Model_Payment_CreditCard
{
	protected $gateway;

	protected $_code = 'ebanx_cc_br';

	protected $_formBlockType = 'ebanx/form_creditcardbr';
	protected $_infoBlockType = 'ebanx/info_creditcardbr';

	public function __construct()
	{
		parent::__construct();

		$this->gateway = $this->ebanx->creditCard();
	}

	public function isAvailable()
	{
		return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_brazil']));
	}
}
