<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';
use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Configs\CreditCardConfig;
use Ebanx\Benjamin\Models\Currency;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Models\Address;
use Ebanx\Benjamin\Models\Person;
use Ebanx\Benjamin\Models\Item;

class Ebanx_Gateway_Model_Api {
    const URL_PRINT_LIVE = 'https://ebanx.com/print/?hash=';
    const URL_PRINT_SANDBOX = 'https://sandbox.ebanx.com/print/?hash=';

    protected $benjamin;

    public function __construct() {
        $config = new Config(array(
            "integrationKey" => Mage::helper('ebanx')->getIntegrationKeyLive(),
            "sandboxIntegrationKey" => Mage::helper('ebanx')->getIntegrationKeySandbox(),
            "isSandbox" => Mage::helper('ebanx')->isSandboxMode(),
            "baseCurrency" => Currency::USD,
            "notificationUrl" => "http://magento.dev/"
        ));
        // Mage::log($config, null, 'benjamin-config.log', true);
        // $creditCardConfig = new CreditCardConfig();
        // $creditCardConfig->addInterest(1,0.2);
        // $this->benjamin = EBANX($config, $creditCardConfig);

        $this->benjamin = EBANX($config);
    }

    public function getEbanxUrl() {
        return Mage::helper('ebanx')->isSandboxMode() ? self::URL_PRINT_SANDBOX : self::URL_PRINT_LIVE;
    }

    public function createCashPayment(Varien_Object $data) {
        $paymentData = new Payment([
            'type' => $data->getEbanxMethod(),
            'address' => new Address([
                'address' => 'Rua Rodrigues',
                'city' => 'Vila Malena d\'Oeste',
                'country' => 'Brasil',
                'state' => 'MS',
                'streetComplement' => 'Apto 35',
                'streetNumber' => '55',
                'zipcode' => '10493-222'
            ]),
            'amountTotal' => 48.63,
            'currencyCode' => 'BRL',
            // 'deviceId' => 'b2017154beac2625eec083a5d45d872f12dc2c57535e25aa149d3bdb57cbdeb9',
            'merchantPaymentCode' => $data->getMerchantPaymentCode() . time(),
            'note' => 'Example payment.',
            'person' => new Person([
                'type' => 'personal',
                'birthdate' => new \DateTime('1978-03-29 08:15:51.000000 UTC'),
                'document' => '38346222653',
                'email' => 'sdasneves@r7.com',
                'ip' => '30.43.84.28',
                'name' => 'Sr. Gustavo Fernando Valência',
                'phoneNumber' =>  '(43) 3965-4162'
            ]),
            'items' => [
                new Item ([
                    'sku' => 'S-NDI-359444',
                    'name' => 'consequuntur perferendis',
                    'description' => 'Aut aliquid quibusdam quidem neque alias aliquid culpa maxime. Totam voluptatum et fuga nesciunt expedita rerum.',
                    'unitPrice' => 7.19,
                    'quantity' => 3,
                    'type' => 'sed'
                ]),
                new Item ([
                    'sku' => 'X-LQF-592041',
                    'name' => 'esse sint',
                    'description' => 'Eligendi error iusto et ut. Cupiditate sint ut et in vitae non.',
                    'unitPrice' => 9.02,
                    'quantity' => 3,
                    'type' => 'est'
                ])
            ],
            // 'responsible' => new Person ([
            //     'type' => 'personal',
            //     'birthdate' => new \DateTime ('1986-06-18 10:18:33 UTC'),
            //     'document' => '38346222653',
            //     'email' => 'alessandra.dominato@galhardo.net',
            //     'ip' => '49.29.237.45',
            //     'name' => 'Luana Aragão Mendes',
            //     'phoneNumber' => '(74) 97063-8157',
            // ]),
            'dueDate' => new \DateTime ($data->getEbanxDueDate()->get('YYYY-MM-dd HH:mm:ss'))
        ]);

        Mage::log($paymentData, null, 'benjamin-boleto.log', true);

        return $this->benjamin->boleto()->create($paymentData);
    }
}
