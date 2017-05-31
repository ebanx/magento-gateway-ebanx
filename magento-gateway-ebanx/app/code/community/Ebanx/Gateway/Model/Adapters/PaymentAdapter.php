<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

use Ebanx\Benjamin\Models\Currency;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Models\Address;
use Ebanx\Benjamin\Models\Person;
use Ebanx\Benjamin\Models\Item;

class Ebanx_Gateway_Model_Adapters_PaymentAdapter
{
	/**
	 * @param Varien_Object $data
	 * @return Payment
	 */
	public function transform(Varien_Object $data)
	{
		$order = $data->getOrder();

		return new Payment([
			'type'                => $data->getEbanxMethod(),
			'amountTotal'         => $data->getAmountTotal(),
			'merchantPaymentCode' => $data->getMerchantPaymentCode(),
			'dueDate'             => $data->getDueDate(),
			'address'             => $this->transformAddress($data->getBillingAddress(), $data),
			'person'              => $this->transformPerson($data->getPerson(), $data),
			'responsible'         => $this->transformPerson($data->getPerson(), $data),
			'items'               => $this->transformItems($data->getItems(), $data)
		]);
	}

	public function transformAddress($address, $data)
	{
		return new Address([
			'address'          => $address->getStreetFull(),
			'city'             => $address->getCity(),
			'country'          => $address->getCountry(),
			'state'            => $address->getRegionCode(),
			'streetComplement' => $address->getStreet2(),
			'zipcode'          => $address->getPostcode()
		]);
	}

	public function transformPerson($person, $data)
	{
		return new Person([
			'type'        => 'personal', // TODO
			'birthdate'   => new \DateTime('1978-03-29 08:15:51.000000 UTC'), // TODO
			'document'    => '52285363451',
			'email'       => $person->getEmail(),
			'ip'          => $data->getRemoteIp(),
			'name'        => "$person->getFirstname() $person->getLastname",
			'phoneNumber' => $data->getTelephone()
		]);
	}

	public function transformItems($items, $data)
	{
		$items = [];

		foreach($items as $item) {
			$items[] = new Item([
				'sku'         => $item->getSku(),
				'name'        => $item->getName(),
				'description' => $item->getDescription(),
				'unitPrice'   => (float) $item->getPrice(),
				'quantity'    => $item->getTotalQtyOrdered()
			]);
		}

		return $items;
	}
}