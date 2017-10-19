<?php

abstract class Ebanx_Gateway_Model_Payment_Creditcard extends Ebanx_Gateway_Payment
{
	protected $_canSaveCc = false;
	private $gatewayFields;

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
		$this->gatewayFields = $this->data->getGatewayFields();
	}

	public function transformPaymentData()
	{
		$this->paymentData = $this->adapter->transformCard($this->data);
	}

	public function processPayment()
	{
		if ($this->gatewayFields['selected_card'] !== 'newcard') {
			$customerId = $this->getOrder()->getCustomerId();
			$selectedCard = $this->gatewayFields['selected_card'];
			$token = $this->gatewayFields['ebanx_token'][$selectedCard];

			if (!Mage::getModel('ebanx/usercard')->doesCardBelongsToCustomer($token, $customerId)){
				$error = Mage::helper('ebanx/error');
				$country = $this->getOrder()->getBillingAddress()->getCountry();
				Mage::throwException($error->getError('GENERAL', $country));
			}
		}

		parent::processPayment();
	}

	public function persistPayment()
	{
		parent::persistPayment();
		$selectedCard = $this->gatewayFields['selected_card'];
		$last4 = substr($this->gatewayFields['ebanx_masked_card_number'][$selectedCard], -4);
		$instalments = array_key_exists('instalments', $this->gatewayFields) ? $this->gatewayFields['instalments'] : 1;
		$this->payment->setInstalments($instalments)
			->setCcLast4($last4)
			->setCcType($this->gatewayFields['ebanx_brand'][$selectedCard]);

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

		if (!isset($this->gatewayFields['ebanx_save_credit_card']) || $this->gatewayFields['ebanx_save_credit_card'] !== 'on') {
			return;
		}

		if ($this->gatewayFields['selected_card'] !== 'newcard') {
			return;
		}

		$customerId = $order->getCustomerId();
		$token = $this->gatewayFields['ebanx_token']['newcard'];
		$brand = $this->gatewayFields['ebanx_brand']['newcard'];
		$maskedCardNumber = $this->gatewayFields['ebanx_masked_card_number']['newcard'];

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
