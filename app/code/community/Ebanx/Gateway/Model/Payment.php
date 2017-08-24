<?php

abstract class Ebanx_Gateway_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
	static protected $redirect_url;

	protected $gateway;
	protected $payment;
	protected $ebanx;
	protected $adapter;
	protected $data;
	protected $result;
	protected $customer;
	protected $paymentData;
	protected $configs;

	protected $_isGateway = true;
	protected $_canUseFormMultishipping = false;
	protected $_isInitializeNeeded = true;
	protected $_canRefund = true;

	public function __construct()
	{
		parent::__construct();

		$this->configs = Mage::getStoreConfig('payment/ebanx_settings');
		$this->ebanx = Mage::getSingleton('ebanx/api')->ebanx();
		$this->adapter = Mage::getModel('ebanx/adapters_paymentadapter');
		$this->helper = Mage::helper('ebanx');
	}

	public function initialize($paymentAction, $stateObject)
	{
		try {
			$this->payment = $this->getInfoInstance();
			$this->order = $this->payment->getOrder();
			$this->customer = Mage::getModel('sales/order')->load($this->order->getId());

			$this->setupData();

			$this->transformPaymentData();

			$this->processPayment();

			$this->persistPayment();

			parent::initialize($paymentAction, $stateObject);
		} catch (Exception $e) {
			Mage::throwException($e->getMessage());
		}
	}

	public function setupData()
	{
		// Create payment data
		$id = $this->payment->getOrder()->getIncrementId();
		$time = time();
		$merchantPaymentCode = "$id-$time";

		$this->data = new Varien_Object();
		$this->data
			->setMerchantPaymentCode($merchantPaymentCode)
			->setOrderId($id)
			->setDueDate($this->helper->getDueDate())
			->setEbanxMethod($this->getCode())
			->setStoreCurrency(Mage::app()->getStore()
			->getCurrentCurrencyCode())
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
		$this->paymentData = $this->adapter->transform($this->data);
	}

	public function processPayment()
	{
		$res = $this->gateway->create($this->paymentData);
		$error = Mage::helper('ebanx/error');

		$this->helper->log($res, $this->getCode());

		if ($res['status'] !== 'SUCCESS') {
			$country = $this->order->getBillingAddress()->getCountry();
			$code = $res['status_code'];

			$this->helper->errorLog($res);
			Mage::throwException($error->getError($code, $country));
		}

		if ($res['payment']['status'] === 'CA') {
			Mage::throwException($error->getError('GENERAL', $country));
		}

		// Set the URL for redirect
		if (!empty($res['redirect_url'])) {
			self::$redirect_url = $res['redirect_url'];
		} else {
			self::$redirect_url = Mage::getUrl('checkout/onepage/success');
		}

		$this->result = $res;
	}

	public function persistPayment()
	{
		$this->payment
			->setEbanxPaymentHash($this->result['payment']['hash'])
			->setEbanxEnvironment($this->helper->getMode())
			->setEbanxDueDate($this->helper->getDueDate($this->order->getCreatedAt()))
			->setEbanxLocalAmount($this->result['payment']['amount_br']);

		if ($this->order->getCustomerId()) {
			Mage::getModel('customer/customer')->load($this->order->getCustomerId())
				->setEbanxCustomerDocument($this->helper->getDocumentNumber($this->order, $this->data))
				->save();
		}
	}

	public function getOrderPlaceRedirectUrl()
	{
		return self::$redirect_url;
	}

	public function isAvailable($quote = null)
	{
		return Mage::getStoreConfig('payment/ebanx_settings/enabled')
		       && $this->helper->areKeysFilled();
	}

	public function canUseForCountry($country)
	{
		$countryName = $this->helper->transformCountryCodeToName($country);

		return $this->gateway->isAvailableForCountry($countryName);
	}

	public function getTotal()
	{
		$quote = $this->getInfoInstance()->getQuote();
		if (!$quote) {
			return $this->getInfoInstance()->getOrder()->getPayment()->getEbanxLocalAmount();
		}
		return $quote->getGrandTotal();
	}
}
