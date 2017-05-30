<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';
use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Configs\CreditCardConfig;
use Ebanx\Benjamin\Models\Currency;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Models\Address;
use Ebanx\Benjamin\Models\Person;
use Ebanx\Benjamin\Models\Item;

class Ebanx_Gateway_Model_Api
{
    const URL_PRINT_LIVE = 'https://ebanx.com/print/?hash=';
    const URL_PRINT_SANDBOX = 'https://sandbox.ebanx.com/print/?hash=';

    protected $benjamin;

    public function __construct() {
        $config = new Config(array(
            'integrationKey' => Mage::helper('ebanx')->getIntegrationKeyLive(),
            'sandboxIntegrationKey' => Mage::helper('ebanx')->getIntegrationKeySandbox(),
            'isSandbox' => Mage::helper('ebanx')->isSandboxMode(),
            'baseCurrency' => Currency::USD,
            'notificationUrl' => "http://magento.dev/"
        ));
        // Mage::log($config, null, 'benjamin-config.log', true);
        // $creditCardConfig = new CreditCardConfig();
        // $creditCardConfig->addInterest(1,0.2);
        // $this->benjamin = EBANX($config, $creditCardConfig);

        $this->benjamin = EBANX($config);
    }

	public function ebanx() {
		return $this->benjamin;
	}

    public function getEbanxUrl() {
        return Mage::helper('ebanx')->isSandboxMode() ? self::URL_PRINT_SANDBOX : self::URL_PRINT_LIVE;
    }
}
