<?php

abstract class Ebanx_Gateway_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
	private $payment;

	protected $ebanx;
	protected $adapter;
	protected $data;
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
	
	function initialize($paymentAction, $stateObject)
	{
		parent::initialize($paymentAction, $stateObject);

		$this->payment = $this->getInfoInstance();
		$order         = $this->payment->getOrder();

		// Create payment data
		$id                  = $this->payment->getOrder()->getIncrementId();
		$time                = time();
		$merchantPaymentCode = "$id-$time";

		$this->data = new Varien_Object();
		$this->data->setMerchantPaymentCode($merchantPaymentCode)
					->setDueDate(Mage::helper('ebanx')->getDueDate())
					->setEbanxMethod($this->_code)
					->setStoreCurrency(Mage::app()->getStore()->getCurrentCurrencyCode())
					->setAmountTotal($order->getGrandTotal())
					->setPerson(Mage::getModel('customer/customer')->load($order->getCustomerId()))
					->setItems($order->getAllVisibleItems())
					->setRemoteIp($order->getRemoteIp())
					->setBillingAddress($order->getBillingAddress())
					->setPayment($this->payment)
					->setOrder($order);
	}
}