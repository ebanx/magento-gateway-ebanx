<?php

abstract class Ebanx_Gateway_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
	protected $payment;
	protected $ebanx;
	protected $adapter;
	protected $data;
	protected $customer;
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
		try {
			parent::initialize($paymentAction, $stateObject);

			$this->payment  = $this->getInfoInstance();
			$this->order    = $this->payment->getOrder();
			$this->customer = Mage::getModel('customer/customer')->load($this->order->getCustomerId());

			$this->setupData();

			$this->processPayment();
		}
		catch (Exception $e) {
			Mage::throwException($e->getMessage());
		}
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

	public function processPayment()
	{
		$paymentData = $this->adapter->transform($this->data);

		// Do request
		$res = $this->gateway->create($paymentData);

		Mage::log(print_r($res, true), null, 'ebanx-' . $this->getCode() . '.log', true);

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

	public function persistPayment()
	{
		$this->payment->setEbanxPaymentHash($this->result['payment']['hash']);
	}

	public function getOrderPlaceRedirectUrl()
	{
		return self::$redirect_url;
	}

	public function canUseForCurrency($currencyCode)
	{
		// TODO: Check the currency using Benjamin, not config.xml

		$allowedCurrencies = explode(',', $this->getConfigData('allowed_currencies'));

		return in_array($currencyCode, $allowedCurrencies);
	}
}
