<?php

abstract class Ebanx_Gateway_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
	private $payment;

	protected $ebanx;
	protected $adapter;

	protected $_isGateway = true;
	protected $_canUseFormMultishipping = false;
	protected $_isInitializeNeeded = true;
	protected $_canRefund = true;

	public function __construct() {
		parent::__construct();

		$this->ebanx = Mage::getSingleton('ebanx/api')->ebanx();
		$this->adapter = Mage::getModel('ebanx/adapters_paymentAdapter');
	}
	
	function initialize($paymentAction, $stateObject) {
		$this->payment = $this->getInfoInstance();

		// Create payment data
		$this->data = new Varien_Object();
		$this->data->setMerchantPaymentCode($this->order->getIncrementId())
					->setEbanxMethod($this->_code)
					->setPayment($this->payment);
	}
}