<?php

abstract class Ebanx_Gateway_Model_Payment_Creditcard extends Ebanx_Gateway_Model_Payment
{
	protected $_canSaveCc = false;

	public function __construct()
	{
		parent::__construct();

		$this->ebanx = Mage::getSingleton('ebanx/api')->ebanxCreditCard();
		$this->gateway = $this->ebanx->creditCard();
	}

	public function getInstalmentTerms($grandTotal = null)
	{
		$amount = $grandTotal ?: $this->getTotal();
		return $this->gateway->getPaymentTermsForCountryAndValue($this->getCountry(), $amount);
	}

	/**
	 * @return string
	 */
	abstract protected function getCountry();

	public function canUseForCountry($country)
	{
		return $this->helper->transformCountryCodeToName($country) === $this->getCountry()
			&& parent::canUseForCountry($country);
	}

	public function setupData()
	{
		parent::setupData();

		$this->data->setGatewayFields(Mage::app()->getRequest()->getPost('payment'));
		$this->data->setPaymentType('cc');
		$this->data->setInstalmentTerms(
			$this->gateway->getPaymentTermsForCountryAndValue(
				$this->helper->transformCountryCodeToName($this->data->getBillingAddress()->getCountry()),
				$this->data->getAmountTotal()
			)
		);
	}

	public function transformPaymentData()
	{
		$this->paymentData = $this->adapter->transformCard($this->data);
	}

	public function persistPayment()
	{
		parent::persistPayment();

		$gatewayFields = $this->data->getGatewayFields();
		$last4 = substr($gatewayFields['ebanx_masked_card_number'], -4);
		$instalments = array_key_exists('instalments', $gatewayFields) ? $gatewayFields['instalments'] : 1;
		$this->payment->setInstalments($instalments)
			->setCcLast4($last4)
			->setCcType($gatewayFields['ebanx_brand']);

		$this->persistCreditCardData();
	}

	private function persistCreditCardData()
	{
		if (!Mage::helper('ebanx')->saveCreditCardAllowed())
		{
			return;
		}

		$order = $this->getOrder();

		if ($order->getCustomerIsGuest()) {
			return;
		}

		$gatewayFields = $this->data->getGatewayFields();

		if (!isset($gatewayFields['ebanx_save_credit_card']) || $gatewayFields['ebanx_save_credit_card'] !== 'on') {
			return;
		}

		$customerId = $order->getCustomerId();
		$token = $gatewayFields['ebanx_token'];
		$brand = $gatewayFields['ebanx_brand'];
		$maskedCardNumber = $gatewayFields['ebanx_masked_card_number'];

		if (!$customerId || !$token || !$brand || !$maskedCardNumber) {
			return;
		}

		$usercard = Mage::getModel('ebanx/usercard');
		if ($usercard->isCardAlreadySavedForCustomer($maskedCardNumber, $customerId)) {
			return;
		}

		Mage::getModel('ebanx/usercard')->setUserId($customerId)
			->setToken($token)
			->setMaskedNumber($maskedCardNumber)
			->setBrand($brand)
			->setPaymentMethod($this->getCode())
			->save();
	}

	/**
	 * @return Mage_Sales_Model_Order
	 */
	private function getOrder()
	{
		$paymentInfo = $this->getInfoInstance();
		$orderId = $paymentInfo->getOrder()->getRealOrderId();

		return Mage::getModel('sales/order')->loadByIncrementId($orderId);
	}
}
