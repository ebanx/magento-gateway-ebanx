<?php

abstract class Ebanx_Gateway_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
	protected $payment;
	protected $ebanx;
	protected $adapter;
	protected $data;
	protected $result;
	static protected $redirect_url;

	protected $_isGateway               = true;
	protected $_canUseFormMultishipping = false;
	protected $_isInitializeNeeded      = true;
	protected $_canRefund               = true;

	public function __construct()
	{
		parent::__construct();

		$this->ebanx   = Mage::getSingleton('ebanx/api')->ebanx();
		$this->adapter = Mage::getModel('ebanx/adapters_paymentAdapter');
	}
	
	public function initialize($paymentAction, $stateObject)
	{
		parent::initialize($paymentAction, $stateObject);

		$this->payment = $this->getInfoInstance();
		$this->order   = $this->payment->getOrder();

		$this->setupData();
		$this->processPayment();
		$this->persistPayment();
	}

	public function setupData()
	{
		// Create payment data
		$id                  = $this->payment->getOrder()->getIncrementId();
		$time                = time();
		$merchantPaymentCode = "$id-$time";

		$this->data = new Varien_Object();
		$this->data->setMerchantPaymentCode($merchantPaymentCode)
					->setDueDate(Mage::helper('ebanx')->getDueDate())
					->setEbanxMethod($this->_code)
					->setStoreCurrency(Mage::app()->getStore()->getCurrentCurrencyCode())
					->setAmountTotal($this->order->getGrandTotal())
					->setPerson(Mage::getModel('customer/customer')->load($this->order->getCustomerId()))
					->setItems($this->order->getAllVisibleItems())
					->setRemoteIp($this->order->getRemoteIp())
					->setBillingAddress($this->order->getBillingAddress())
					->setPayment($this->payment)
					->setOrder($this->order);
	}

	public function processPayment()
	{
		$payment = $this->adapter->transform($this->data);

		// Do request
		$res = $this->gateway->create($payment);

		Mage::log($res, null, $this->_code . '.log', true);

		if ($res['status'] !== 'SUCCESS') {
			// TODO: Make an error handler
			Mage::throwException($res['status_message']);
		}

		// Set the URL for redirect
		if (!empty($res['redirect_url'])) {
			self::$redirect_url = $res['redirect_url'];
		}
		else {
			self::$redirect_url = Mage::getUrl('checkout/onepage/success');
		}

		$this->result = $res;
	}

	public function persistPayment()
	{
		$this->payment->setEbanxPaymentHash($this->result['payment']['hash']);
	}

	public function getOrderPlaceRedirectUrl()
	{
		return self::$redirect_url;
	}
}