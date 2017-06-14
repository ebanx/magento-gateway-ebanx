<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

use Ebanx\Benjamin\Models\Bank;
use Ebanx\Benjamin\Models\Country;

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

		foreach ($fields as $field) {
			if (!Mage::getStoreConfig('payment/ebanx_settings/' . $field)) {
				return false;
			}
		}

		return true;
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
}
