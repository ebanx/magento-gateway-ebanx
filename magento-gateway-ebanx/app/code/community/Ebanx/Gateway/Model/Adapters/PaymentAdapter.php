<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

use Ebanx\Benjamin\Models\Address;
use Ebanx\Benjamin\Models\Item;
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

	public function transformAddress($address, $data)
	{
		return new Address([
			'address' => $address->getStreetFull(),
			'streetNumber' => '123', // TODO
			'city' => $address->getCity(),
			'country' => $this->helper->transformCountryCodeToName($address->getCountry()),
			'state' => $address->getRegionCode(),
			'streetComplement' => $address->getStreet2(),
			'zipcode' => $address->getPostcode()
		]);
	}

	public function transformPerson($person, $data)
	{
		return new Person([
			'type' => 'personal', // TODO
			'birthdate' => new \DateTime('1978-03-29 08:15:51.000000 UTC'), // TODO
			'document' => '07834442902',
			'email' => $person->getEmail(),
			'ip' => $data->getRemoteIp(),
			'name' => $data->getPerson()->getFirstname() . ' ' . $data->getPerson()->getLastname(),
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
