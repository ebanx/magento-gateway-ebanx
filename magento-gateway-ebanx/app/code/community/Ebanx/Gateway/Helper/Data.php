<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

use Ebanx\Benjamin\Models\Bank;
use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Person;

class Ebanx_Gateway_Helper_Data extends Mage_Core_Helper_Abstract
{
	const URL_PRINT_LIVE = 'https://ebanx.com/print/';
	const URL_PRINT_SANDBOX = 'https://sandbox.ebanx.com/print/';

	public function getEbanxUrl()
	{
		return $this->isSandboxMode() ? self::URL_PRINT_SANDBOX : self::URL_PRINT_LIVE;
	}

	public function isSandboxMode()
	{
		return $this->getMode() === Ebanx_Gateway_Model_Source_Mode::SANDBOX;
	}

	public function getMode()
	{
		return Mage::getStoreConfig('payment/ebanx_settings/mode');
	}

	public function getIntegrationKey()
	{
		return Mage::getStoreConfig('payment/ebanx_settings/integration_key_' . $this->getMode());
	}

	public function getSandboxIntegrationKey()
	{
		return Mage::getStoreConfig('payment/ebanx_settings/integration_key_' . Ebanx_Gateway_Model_Source_Mode::SANDBOX);
	}

	public function getLiveIntegrationKey()
	{
		return Mage::getStoreConfig('payment/ebanx_settings/integration_key_' . Ebanx_Gateway_Model_Source_Mode::LIVE);
	}

	public function getDueDate()
	{
		$dueDate = new Zend_Date(Mage::getModel('core/date')->timestamp());
		return $dueDate->addDay($this->getDueDateDays())->get('YYYY-MM-dd HH:mm:ss');
	}

	public function getDueDateDays()
	{
		return Mage::getStoreConfig('payment/ebanx_settings/due_date_days');
	}

	public function transformCountryCodeToName($countryCode)
	{
		if (!$countryCode) {
			return false;
		}

		$countries = [
			'cl' => Country::CHILE,
			'br' => Country::BRAZIL,
			'co' => Country::COLOMBIA,
			'mx' => Country::MEXICO,
			'pe' => Country::PERU,
		];

		return $countries[strtolower($countryCode)];
	}

	public function transformTefToBankName($bankCode)
	{
		$banks = [
			'itau' => Bank::ITAU,
			'bradesco' => Bank::BRADESCO,
			'bancodobrasil' => Bank::BANCO_DO_BRASIL,
			'banrisul' => Bank::BANRISUL
		];

		return $banks[strtolower($bankCode)];
	}

	public function hasComplianceFieldFromSettings($code)
	{
		$methodsToFields = [
			'ebanx_boleto' => ['cpf_field', 'cnpj_field'],
			'ebanx_tef' => ['cpf_field', 'cnpj_field'],
			'ebanx_wallet' => ['cpf_field', 'cnpj_field'],
			'ebanx_cc_br' => ['cpf_field', 'cnpj_field'],
			'ebanx_sencillito' => ['rut_field'],
			'ebanx_servipag' => ['rut_field'],
			'ebanx_baloto' => ['dni_field'],
			'ebanx_pse' => ['dni_field'],
			'ebanx_oxxo' => [],
			'ebanx_cc_mx' => [],
			'ebanx_dc_mx' => [],
			'ebanx_pagoefectivo' => [],
			'ebanx_safetypay' => []
		];

		$fields = $methodsToFields[$code];

		if (empty($fields)) {
			return true;
		}

		foreach ($fields as $field) {
			if (Mage::getStoreConfig('payment/ebanx_settings/' . $field)) {
				return true;
			}
		}

		return false;
	}

	public function getBrazilianDocumentLabel()
	{
		$label = [];
		$taxes = explode(',', Mage::getStoreConfig('payment/ebanx_settings/brazil_taxes'));

		return strtoupper(implode(' / ', $taxes));
	}

	public function getLabelForComplianceField($code)
	{
		switch ($code) {
			case 'ebanx_boleto':
			case 'ebanx_tef':
			case 'ebanx_wallet':
			case 'ebanx_cc_br':
				return $this->getBrazilianDocumentLabel();

			case 'ebanx_sencillito':
			case 'ebanx_servipag':
				return $this->__('RUT Document');

			case 'ebanx_baloto':
			case 'ebanx_pse':
				return $this->__('DNI Document');

			default:
				return $this->__('Document Number');
		}
	}

	public function getBrazilianDocumentNumber()
	{
		$customer = $this->getCustomerData();

		if ($cpfField = Mage::getStoreConfig('payment/ebanx_settings/cpf_field')) {
			if ($customer[$cpfField]) {
				return $customer[$cpfField];
			}
		}

		if ($cnpjField = Mage::getStoreConfig('payment/ebanx_settings/cnpj_field')) {
			if ($customer[$cnpjField]) {
				return $customer[$cnpjField];
			}
		}


		return $customer['ebanx-document'];
	}

	public function getChileanDocumentNumber()
	{
		$customer = $this->getCustomerData();

		if ($rutField = Mage::getStoreConfig('payment/ebanx_settings/rut_field')) {
			if ($customer[$rutField]) {
				return $customer[$rutField];
			}
		}

		return $customer['ebanx-document'];
	}

	public function getColombianDocumentNumber()
	{
		$customer = $this->getCustomerData();

		if ($rutField = Mage::getStoreConfig('payment/ebanx_settings/rut_field')) {
			if ($customer[$rutField]) {
				return $customer[$rutField];
			}
		}

		return $customer['ebanx-document'];
	}

	public function getDocumentNumber()
	{
		$countryCode = $this->getCustomerData()['country_id'];
		$country = $this->transformCountryCodeToName($countryCode);

		switch ($country) {
			case Country::BRAZIL:
				return $this->getBrazilianDocumentNumber();
			case Country::CHILE:
				return $this->getChileanDocumentNumber();
			case Country::COLOMBIA:
				return $this->getColombianDocumentNumber();
			default:
				return null;

		}
	}

	private function getCustomerData()
	{
		$checkoutData = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->getData();

		$customerAddressData = array_key_exists('customer_address_id', $checkoutData)
			? Mage::getModel('customer/address')->load($checkoutData['customer_address_id'])->getCustomer()->getData()
			: $checkoutData['customer_address']->getCustomer()->getData();

		$customerSessionData = Mage::getSingleton('customer/session')->getCustomer()->getData();

		$customerParams = Mage::app()->getRequest()->getParams();

		return array_merge(
			$checkoutData,
			$customerAddressData,
			$customerSessionData,
			$customerParams
		);
	}
}
