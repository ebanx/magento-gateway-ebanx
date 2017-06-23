<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Configs\CreditCardConfig;

class Ebanx_Gateway_Model_Api
{
	protected $ebanx;
	protected $config;

	public function getConfig()
	{
		return new Config(array(
			'integrationKey' => Mage::helper('ebanx')->getLiveIntegrationKey(),
			'sandboxIntegrationKey' => Mage::helper('ebanx')->getSandboxIntegrationKey(),
			'isSandbox' => Mage::helper('ebanx')->isSandboxMode(),
			'baseCurrency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
			'notificationUrl' => Mage::getUrl('ebanx/index/notification/'),
			'redirectUrl' => Mage::getUrl('ebanx/index/notification/'),
		));
	}

	public function __construct()
	{
		$this->ebanx = EBANX($this->getConfig());
	}

	public function ebanx()
	{
		return $this->ebanx;
	}

	public function ebanxCreditCard()
	{
		$creditCardConfig = new CreditCardConfig(array(
			'maxInstalments'      => Mage::helper('ebanx')->getMaxInstalments(),
			'minInstalmentAmount' => Mage::helper('ebanx')->getMinInstalmentValue(),
			'interestRates'       => 0,
		));

		return $this->ebanx->addConfig($creditCardConfig);
	}
}
