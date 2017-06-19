<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

use Ebanx\Benjamin\Models\Address;
use Ebanx\Benjamin\Models\Item;
use Ebanx\Benjamin\Models\Card;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Models\Person;

class Ebanx_Gateway_Model_Adapters_PaymentAdapter
{
	private $helper;

	public function __construct()
	{
		$this->helper = Mage::helper('ebanx');
	}

	/**
	 * @param Varien_Object $data
	 * @return Payment
	 */
	public function transform(Varien_Object $data)
	{
		return new Payment([
			'type' => $data->getEbanxMethod(),
			'amountTotal' => $data->getAmountTotal(),
			'merchantPaymentCode' => $data->getMerchantPaymentCode(),
			'dueDate' => $data->getDueDate(),
			'address' => $this->transformAddress($data->getBillingAddress(), $data),
			'person' => $this->transformPerson($data->getPerson(), $data),
			'responsible' => $this->transformPerson($data->getPerson(), $data),
			'items' => $this->transformItems($data->getItems(), $data)
		]);
	}

	public function transformCard(Varien_Object $data)
	{
		$gatewayFields = Mage::app()->getRequest()->getPost('payment');

		$payment = $this->transform($data);
		$payment->deviceId = $gatewayFields['ebanx_device_fingerprint'];

		$payment->card = new Card([
			'autoCapture' => true,
			'cvv' => $gatewayFields['cc_cid'],
			'dueDate' => DateTime::createFromFormat('n-Y', $gatewayFields['cc_exp_month'] . '-' . $gatewayFields['cc_exp_year']),
			'name' => $gatewayFields['cc_name'],
			'token' => $gatewayFields['ebanx_token'],
			'type' => $gatewayFields['ebanx_brand'],
		]);

		return $payment;
	}

	public function transformAddress($address, $data)
	{
		$street = $this->helper->split_street($address->getStreetFull());

		return new Address([
			'address' => $street['streetName'],
			'streetNumber' => $street['houseNumber'],
			'city' => $address->getCity(),
			'country' => $this->helper->transformCountryCodeToName($address->getCountry()),
			'state' => $address->getRegionCode(),
			'streetComplement' => $address->getStreet2(),
			'zipcode' => $address->getPostcode()
		]);
	}

	public function transformPerson($person, $data)
	{
		$document = $this->helper->getDocumentNumber($data->getOrder());

		return new Person([
			'type' => $this->helper->getPersonType($document),
			'document' => $document,
			'email' => $person->getCustomerEmail(),
			'ip' => $data->getRemoteIp(),
			'name' => $person->getCustomerFirstname() . ' ' . $person->getCustomerLastname(),
			'phoneNumber' => $data->getBillingAddress()->getTelephone()
		]);
	}

	public function transformItems($items, $data)
	{
		$itemsData = [];

		foreach ($items as $item) {
			$itemsData[] = new Item([
				'sku' => $item->getSku(),
				'name' => $item->getName(),
				'description' => $item->getDescription(),
				'unitPrice' => $item->getPrice(),
				'quantity' => $item->getTotalQtyOrdered()
			]);
		}

		return $itemsData;
	}
}
