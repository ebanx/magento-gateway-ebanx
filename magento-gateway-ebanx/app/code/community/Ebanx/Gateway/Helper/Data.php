<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Bank;

class Ebanx_Gateway_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getMode()
	{
        return Mage::getStoreConfig('payment/ebanx_settings/mode');
    }

    public function isSandboxMode()
	{
        return $this->getMode() === Ebanx_Gateway_Model_Source_Mode::SANDBOX;
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

    public function getDueDateDays()
	{
        return Mage::getStoreConfig('payment/ebanx_settings/due_date_days');
    }

    public function getDueDate()
	{
        $dueDate = new Zend_Date(Mage::getModel('core/date')->timestamp());
        return $dueDate->addDay($this->getDueDateDays())->get('YYYY-MM-dd HH:mm:ss');
    }

	public function transformCountryCodeToName($countryCode)
	{
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
}
