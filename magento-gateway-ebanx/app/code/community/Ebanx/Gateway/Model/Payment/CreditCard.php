<?php

abstract class Ebanx_Gateway_Model_Payment_CreditCard extends Ebanx_Gateway_Model_Payment
{
	protected $gateway;
	protected $payment;
	protected $ebanx;
	protected $adapter;
	protected $data;
	protected $result;
	protected $customer;
	protected $paymentData;
	static protected $redirect_url;

	protected $_canSaveCc     			= false;
	protected $_isGateway               = true;
	protected $_canUseFormMultishipping = false;
	protected $_isInitializeNeeded      = true;
	protected $_canRefund               = true;

	public function __construct()
	{
		parent::__construct();

		$this->ebanx   = Mage::getSingleton('ebanx/api')->ebanxCreditCard();
		$this->adapter = Mage::getModel('ebanx/adapters_paymentAdapter');
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
					->setPerson($this->customer)
					->setItems($this->order->getAllVisibleItems())
					->setRemoteIp($this->order->getRemoteIp())
					->setBillingAddress($this->order->getBillingAddress())
					->setPayment($this->payment)
					->setOrder($this->order);
	}

	public function transformPaymentData()
	{
		$this->paymentData = $this->adapter->transformCard($this->data);
	}

	public function processPayment()
	{
		// Do request
		$res = $this->gateway->create($this->paymentData);

		Mage::log(print_r($res, true), null, $this->getCode() . '.log', true);

		if ($res['status'] !== 'SUCCESS') {
			// TODO: Make an error handler
			Mage::throwException($res['status_code'] . ' - ' . $res['status_message']);
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
}
