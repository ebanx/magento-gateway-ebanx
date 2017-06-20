<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Configs\CreditCardConfig;

class Ebanx_Gateway_Model_Api
{
	protected $benjamin;
    protected $config;

	public function getConfig()
	{
		return new Config(array(
			'integrationKey' => Mage::helper('ebanx')->getLiveIntegrationKey(),
			'sandboxIntegrationKey' => Mage::helper('ebanx')->getSandboxIntegrationKey(),
			'isSandbox' => Mage::helper('ebanx')->isSandboxMode(),
			'baseCurrency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
			'notificationUrl' => Mage::getBaseUrl(),
			'redirectUrl' => Mage::getBaseUrl(),
		));
	}

	public function __construct()
	{
		$config = new Config(array(
			'integrationKey' => Mage::helper('ebanx')->getLiveIntegrationKey(),
			'sandboxIntegrationKey' => Mage::helper('ebanx')->getSandboxIntegrationKey(),
			'isSandbox' => Mage::helper('ebanx')->isSandboxMode(),
			'baseCurrency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
			'notificationUrl' => Mage::getUrl('ebanx/index/notification/'),
			'redirectUrl' => Mage::getUrl('ebanx/index/notification/'),
		));

		$this->benjamin = EBANX($config);
	}

	public function ebanx()
	{
		return EBANX($this->getConfig());
	}

	public function ebanxCreditCard()
	{
		$creditCardConfig = new CreditCardConfig(array(
			'maxInstalments'      => Mage::helper('ebanx')->getMaxInstalments(),
			'minInstalmentAmount' => Mage::helper('ebanx')->getMinInstalmentValue(),
			'interestRates'       => 0,
		));

		return EBANX($this->getConfig(), $creditCardConfig);
	}
}
