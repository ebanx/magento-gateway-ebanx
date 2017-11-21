<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Configs\CreditCardConfig;

class Ebanx_Gateway_Model_Api
{
	protected $ebanx;
	protected $config;

	public function __construct()
	{
		$this->ebanx = EBANX($this->getConfig(), $this->getCreditCardConfig());
	}

	public function getConfig()
	{
		return new Config(array(
			'integrationKey' => Mage::helper('ebanx')->getLiveIntegrationKey(),
			'sandboxIntegrationKey' => Mage::helper('ebanx')->getSandboxIntegrationKey(),
			'isSandbox' => Mage::helper('ebanx')->isSandboxMode(),
//			'baseCurrency' => Mage::app()->getStore()->getBaseCurrencyCode(),
			'baseCurrency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
			'notificationUrl' => Mage::getUrl('ebanx/index/notification/'),
			'redirectUrl' => Mage::getUrl('checkout/onepage/success'),
			'userValues' => array(
				1 => 'from_magento',
				3 => 'version=1.0.0', //TODO: Create a method to get the current version
//				3 => (string)Mage::getConfig()->getNode('modules/MyModuleName/version'),
			),
		));
	}

	/**
	 * @return CreditCardConfig
	 */
	private function getCreditCardConfig()
	{
		$creditCardConfig = new CreditCardConfig(array(
			'maxInstalments' => Mage::helper('ebanx')->getMaxInstalments(),
			'minInstalmentAmount' => Mage::helper('ebanx')->getMinInstalmentValue(),
		));

		$interestRate = unserialize(Mage::helper('ebanx')->getInterestRate());
		usort($interestRate, function ($value, $previous) {
			if ($value['instalments'] === $previous['instalments']) {
				return 0;
			}

			return ($value['instalments'] < $previous['instalments']) ? -1 : 1;
		});


		for ($i = 1; $i <= Mage::helper('ebanx')->getMaxInstalments(); $i++) {
			foreach ($interestRate as $interestConfig) {
				if ($i <= $interestConfig['instalments']) {
					$creditCardConfig->addInterest($i, $interestConfig['interest']);
					break;
				}
			}
		}
		return $creditCardConfig;
	}

	public function ebanx()
	{
		return $this->ebanx;
	}

	public function ebanxCreditCard()
	{
		return $this->ebanx;
	}
}
