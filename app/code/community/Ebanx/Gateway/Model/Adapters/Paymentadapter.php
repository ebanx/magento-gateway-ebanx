<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

use Ebanx\Benjamin\Models\Address;
use Ebanx\Benjamin\Models\Card;
use Ebanx\Benjamin\Models\Item;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Models\Person;

class Ebanx_Gateway_Model_Adapters_Paymentadapter
{
	private $helper;

	private $states = array(
		'alagoas' => 'AL',
		'amapa' => 'AP',
		'amazonas' => 'AM',
		'bahia' => 'BA',
		'ceara' => 'CE',
		'distrito federal' => 'DF',
		'espirito santo' => 'ES',
		'goias' => 'GO',
		'maranhao' => 'MA',
		'mato grosso' => 'MT',
		'mato grosso do sul' => 'MS',
		'minas gerais' => 'MG',
		'para' => 'PA',
		'paraiba' => 'PB',
		'parana' => 'PR',
		'pernambuco' => 'PE',
		'piaui' => 'PI',
		'rio de janeiro' => 'RJ',
		'rio grande do norte' => 'RN',
		'rio grande do sul' => 'RS',
		'rondonia' => 'RO',
		'roraima' => 'RR',
		'santa catarina' => 'SC',
		'sao paulo' => 'SP',
		'sergipe' => 'SE',
		'tocantins' => 'TO',
	);

	public function __construct()
	{
		$this->helper = Mage::helper('ebanx');
	}

	public function transformCard(Varien_Object $data)
	{
		$gatewayFields = $data->getGatewayFields();
		$instalmentTerms = $data->getInstalmentTerms();

		$payment = $this->transform($data);

		$selectedCard = $gatewayFields['selected_card'];
		$payment->deviceId = $gatewayFields['ebanx_device_fingerprint'][$selectedCard];

		if (isset($gatewayFields['instalments'])) {
			$payment->instalments = $gatewayFields['instalments'];
			$term = $instalmentTerms[$gatewayFields['instalments'] - 1];
			$payment->amountTotal = $term->baseAmount * $term->instalmentNumber;
		}

		$code = $data->getPaymentType();

		$payment->card = new Card(array(
			'autoCapture' => $this->shouldAutoCapture(),
			'cvv' => $gatewayFields[$code . '_cid'][$selectedCard],
			'dueDate' => $this->transformDueDate($gatewayFields, $code),
			'name' => $gatewayFields[$code . '_name'][$selectedCard],
			'token' => $gatewayFields['ebanx_token'][$selectedCard],
			'type' => $gatewayFields['ebanx_brand'][$selectedCard],
		));

		if ($data->getBillingAddress()->getCountry() === 'AR') {
			$payment->card->type = 'mastercard';
		}

		return $payment;
	}

	/**
	 * @return boolean
	 */
	private function shouldAutoCapture() {
		return Mage::getStoreConfig('payment/ebanx_settings/auto_capture') === '1';
	}

	/**
	 * @param Varien_Object $data
	 * @return Payment
	 */
	public function transform(Varien_Object $data)
	{
		return new Payment(array(
			'type' => $data->getEbanxMethod(),
			'amountTotal' => $data->getAmountTotal(),
			'merchantPaymentCode' => $data->getMerchantPaymentCode(),
			'orderNumber' => $data->getOrderId(),
			'dueDate' => new \DateTime($data->getDueDate()),
			'address' => $this->transformAddress($data->getBillingAddress(), $data),
			'person' => $this->transformPerson($data->getPerson(), $data),
			'responsible' => $this->transformPerson($data->getPerson(), $data),
			'items' => $this->transformItems($data->getItems(), $data),
			'riskProfileId' => $this->transformRiskProfileId(),
		));
	}

	/**
	 * @param Varien_Object $address
	 * @param Varien_Object $data
	 * @return Address
	 */
	public function transformAddress($address, $data)
	{
		$street = $this->helper->split_street($address->getStreet1());
		$state = $address->getRegion();

		$streetNumberField = Mage::getStoreConfig('payment/ebanx_settings/street_number_field');
		if ($streetNumberField && isset($address->getData()[$streetNumberField])) {
			$street['houseNumber'] = $address->getData()[$streetNumberField];
		}

		if ($address->getCountry() === 'BR') {
			$state = preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($state, ENT_QUOTES, 'UTF-8'));
			if (array_key_exists(strtolower($state), $this->states)){
				$state = $this->states[strtolower($state)];
			}
		}

		return new Address(array(
			'address' => $street['streetName'],
			'streetNumber' => $street['houseNumber'],
			'city' => $address->getCity(),
			'country' => $this->helper->transformCountryCodeToName($address->getCountry()),
			'state' => $state,
			'streetComplement' => $address->getStreet2(),
			'zipcode' => $address->getPostcode()
		));
	}

	public function transformPerson($person, $data)
	{
		$document = $this->helper->getDocumentNumber($data->getOrder(), $data);

		$email = $person->getCustomerEmail() ?: $data->getBillingAddress()->getEmail();

		$name = $person->getCustomerFirstname() || $person->getCustomerLastname()
			? $person->getCustomerFirstname() . ' ' . $person->getCustomerLastname()
			: $data->getBillingAddress()->getName();

		return new Person(array(
			'type' => $this->helper->getPersonType($document),
			'document' => $document,
			'documentType' => $this->helper->getDocumentType($data->getBillingAddress()->getCountry()),
			'email' => $email,
			'ip' => $data->getRemoteIp(),
			'name' => $name,
			'phoneNumber' => $data->getBillingAddress()->getTelephone()
		));
	}

	public function transformItems($items, $data)
	{
		$itemsData = array();

		foreach ($items as $item) {
			$product = $item->getProduct();

			$itemsData[] = new Item(array(
				'sku' => $item->getSku(),
				'name' => $item->getName(),
				'unitPrice' => $product->getPrice(),
				'quantity' => $item->getQtyToInvoice()
			));
		}

		return $itemsData;
	}

	/**
	 * @param array $gatewayFields
	 * @param string $code
	 *
	 * @return bool|DateTime
	 */
	private function transformDueDate($gatewayFields, $code) {
		$selectedCard = $gatewayFields['selected_card'];
		$month = 1;
		if (array_key_exists($code . '_exp_month', $gatewayFields) && is_array($gatewayFields[$code . '_exp_month']) && array_key_exists($selectedCard, $gatewayFields[$code . '_exp_month'])) {
			$month = $gatewayFields[$code . '_exp_month'][$selectedCard] ?: 1;
		}

		$year = 2120;
		if (array_key_exists($code . '_exp_year', $gatewayFields) && is_array($gatewayFields[$code . '_exp_year']) && array_key_exists($selectedCard, $gatewayFields[$code . '_exp_year'])) {
			$year = $gatewayFields[$code . '_exp_year'][$selectedCard] ?: 2120;
		}

		return DateTime::createFromFormat( 'n-Y', $month . '-' . $year );
	}

	private function transformRiskProfileId() {
		$version = 'Mx' . Mage::getConfig()->getNode('modules/Ebanx_Gateway/version');

		return preg_replace('/\./', 'x', $version);
	}
}
