<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Configs\CreditCardConfig;

class Ebanx_Gateway_Model_Api
{
    const URL_PRINT_LIVE    = 'https://ebanx.com/print/?hash=';
    const URL_PRINT_SANDBOX = 'https://sandbox.ebanx.com/print/?hash=';

    protected $benjamin;
    protected $config;

	public function getConfig()
	{
		return new Config(array(
            'integrationKey'        => Mage::helper('ebanx')->getLiveIntegrationKey(),
            'sandboxIntegrationKey' => Mage::helper('ebanx')->getSandboxIntegrationKey(),
            'isSandbox'             => Mage::helper('ebanx')->isSandboxMode(),
            'baseCurrency'          => Mage::app()->getStore()->getCurrentCurrencyCode(),
            'notificationUrl'       => Mage::getBaseUrl(),
            'redirectUrl'           => Mage::getBaseUrl(),
        ));
	}

	public function ebanx()
	{
		return EBANX($this->getConfig());
	}

	public function ebanxCreditCard()
	{
		$creditCardConfig = new CreditCardConfig(array(
			'maxInstalments'      => 12,
			'minInstalmentAmount' => 20,
			'interestRates'       => 0,
		));

		return EBANX($this->getConfig(), $creditCardConfig);
	}

    public function getEbanxUrl()
	{
        return Mage::helper('ebanx')->isSandboxMode() ? self::URL_PRINT_SANDBOX : self::URL_PRINT_LIVE;
    }
}
