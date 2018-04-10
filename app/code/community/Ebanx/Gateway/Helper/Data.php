<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

use Ebanx\Benjamin\Models\Bank;
use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Person;
use Ebanx\Benjamin\Services\Exchange;

class Ebanx_Gateway_Helper_Data extends Mage_Core_Helper_Abstract
{
    const URL_PRINT_LIVE = 'https://print.ebanx.com/';
    const URL_PRINT_SANDBOX = 'https://sandbox.ebanx.com/print/';

    private $order;

    /**
     * @return string
     */
    public function getEbanxUrl()
    {
        return $this->isSandboxMode() ? self::URL_PRINT_SANDBOX : self::URL_PRINT_LIVE;
    }

    /**
     * @return bool
     */
    public function isSandboxMode()
    {
        return $this->getMode() === Ebanx_Gateway_Model_Source_Mode::SANDBOX;
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/mode');
    }

    /**
     * @return mixed
     */
    public function getSandboxIntegrationKey()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/integration_key_' . Ebanx_Gateway_Model_Source_Mode::SANDBOX);
    }

    /**
     * @return mixed
     */
    public function getLiveIntegrationKey()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/integration_key_' . Ebanx_Gateway_Model_Source_Mode::LIVE);
    }

    /**
     * @return bool
     */
    public function areKeysFilled()
    {
        $keys = $this->getIntegrationKey();
        $publicKeys = $this->getPublicIntegrationKey();
        return !empty($keys) && !empty($publicKeys);
    }

    /**
     * @return mixed
     */
    public function getIntegrationKey()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/integration_key_' . $this->getMode());
    }

    /**
     * @return mixed
     */
    public function getPublicIntegrationKey()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/integration_key_public_' . $this->getMode());
    }

    /**
     * @param string $date   Date if not currently
     * @param string $format Format desired to the date
     * @return mixed
     */
    public function getDueDate($date = null, $format = 'YYYY-MM-dd HH:mm:ss')
    {
        $date = !is_null($date) ? $date : Mage::getModel('core/date')->timestamp();
        $dueDate = new Zend_Date($date);

        return $dueDate->addDay($this->getDueDateDays())->get($format);
    }

    /**
     * @return mixed
     */
    public function getDueDateDays()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/due_date_days');
    }

    /**
     * @return mixed
     */
    public function getMaxInstalments()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/max_instalments');
    }

    /**
     * @return mixed
     */
    public function getMinInstalmentValue()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/min_instalment_value');
    }

    /**
     * @return mixed
     */
    public function getInterestRate()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/interest_rate');
    }

    /**
     * @return mixed
     */
    public function saveCreditCardAllowed()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/save_card_data');
    }

    /**
     * @param string $bankCode Code of the bank
     * @return mixed
     */
    public function transformTefToBankName($bankCode)
    {
        $banks = array(
            'itau' => Bank::ITAU,
            'bradesco' => Bank::BRADESCO,
            'bancodobrasil' => Bank::BANCO_DO_BRASIL,
            'banrisul' => Bank::BANRISUL
        );

        return $banks[strtolower($bankCode)];
    }

    /**
     * @param string $methodCode Method code
     * @return bool
     */
    public function hasDocumentFieldAlreadyForMethod($methodCode)
    {
        $fields = $this->getDocumentFieldsRequiredForMethod($methodCode);

        if (empty($fields)) {
            return true;
        }

        foreach ($fields as $field) {
            $documentFieldName = Mage::getStoreConfig('payment/ebanx_settings/' . $field);
            if ($documentFieldName) {
                if (!Mage::getSingleton('customer/session')->isLoggedIn()
                    || Mage::getSingleton('checkout/session')->getQuote()->getCheckoutMethod() === 'register') {
                    return true;
                }

                $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
                $customer = Mage::getModel('customer/customer')->load($customerId);

                $customerHasSavedAddress = $customer->getDefaultShipping();
                $customerHasSavedDocument = $customer->getData($documentFieldName);
                if ($customerHasSavedAddress && $customerHasSavedDocument) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $code Code of ebanx payment method
     * @return string
     */
    public function getLabelForComplianceField($code)
    {
        switch ($code) {
            case 'ebanx_boleto':
            case 'ebanx_tef':
            case 'ebanx_wallet':
            case 'ebanx_cc_br':
                return $this->getBrazilianDocumentLabel();

            case 'ebanx_webpay':
                return $this->__('RUT Document');

            case 'ebanx_pagoefectivo':
            case 'ebanx_safetypay':
            case 'ebanx_cc_co':
                return $this->__('DNI Document');

            case 'ebanx_pagofacil':
            case 'ebanx_rapipago':
            case 'ebanx_cupon':
            case 'ebanx_cc_ar':
                return 'Documento';

            default:
                return $this->__('Document Number');
        }
    }

    /**
     * @param string $countryCode Country string
     * @return string
     */
    public function getLabelForComplianceFieldByCountry($countryCode)
    {
        switch (strtolower($countryCode)) {
            case 'br':
                return $this->getBrazilianDocumentLabel();
            case 'cl':
                return $this->__('RUT Document');
            case 'pe':
            case 'co':
                return $this->__('DNI Document');
            case 'ar':
                return 'Documento';
            default:
                return $this->__('Document Number');
        }
    }

    /**
     * @return string
     */
    public function getBrazilianDocumentLabel()
    {
        $label = array();
        $taxes = explode(',', Mage::getStoreConfig('payment/ebanx_settings/brazil_taxes'));

        return strtoupper(implode(' / ', $taxes));
    }

    /**
     * @param object $order Some order
     * @param object $data  Ebanx document
     * @return null|string
     */
    public function getDocumentNumber($order, $data)
    {
        $this->order = $order;
        $countryCode = $this->getCustomerData()['country_id'];
        $country = $this->transformCountryCodeToName($countryCode);
        $methodCode = $data->getEbanxMethod();

        switch ($country) {
            case Country::BRAZIL:
                return $this->getBrazilianDocumentNumber($methodCode);
            case Country::CHILE:
                return $this->getChileanDocumentNumber($methodCode);
            case Country::COLOMBIA:
                return $this->getColombianDocumentNumber($methodCode);
            case Country::ARGENTINA:
                return $this->getArgetinianDocument($methodCode);
            case Country::PERU:
                return $this->getPeruvianDocumentNumber($methodCode);
            default:
                return null;
        }
    }

    /**
     * @return array
     */
    public function getCustomerData()
    {
        $checkoutData = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->getData();

        $customerAddressData = array_key_exists('customer_address_id', $checkoutData) && !is_null($checkoutData['customer_address_id'])
            ? Mage::getModel('customer/address')->load($checkoutData['customer_address_id'])->getCustomer()->getData()
            : $checkoutData;

        $customerSessionData = Mage::getSingleton('customer/session')->getCustomer()->getData();

        $customerParams = Mage::app()->getRequest()->getParams();

        $data = array_merge(
            $checkoutData,
            $customerAddressData,
            $customerSessionData,
            $customerParams
        );

        return $data;
    }

    /**
     * @param string $countryCode ISO country code
     * @return bool|mixed|null
     */
    public function transformCountryCodeToName($countryCode)
    {
        if (!$countryCode) {
            return false;
        }

        $country = Country::fromIso($countryCode);

        if (!$country) {
            return false;
        }

        return $country;
    }

    /**
     * @param $string $methodCode EBANX method code
     * @return mixed
     */
    public function getBrazilianDocumentNumber($methodCode)
    {
        $customer = $this->getCustomerData();

        if (array_key_exists('ebanx-document', $customer) && isset($customer['ebanx-document'][$methodCode])) {
            return $customer['ebanx-document'][$methodCode];
        }

        if ($cpfField = Mage::getStoreConfig('payment/ebanx_settings/cpf_field')) {
            if ($cpfField === 'taxvat') {
                return $this->order->getCustomerTaxvat();
            }

            if ($customer[$cpfField]) {
                return $customer[$cpfField];
            }
        }

        if ($cnpjField = Mage::getStoreConfig('payment/ebanx_settings/cnpj_field')) {
            if ($cnpjField === 'taxvat') {
                return $this->order->getCustomerTaxvat();
            }

            if ($customer[$cnpjField]) {
                return $customer[$cnpjField];
            }
        }


        return $customer['ebanx-document'][$methodCode];
    }

    /**
     * @param string $methodCode EBANX method code
     * @return mixed|string
     */
    public function getChileanDocumentNumber($methodCode)
    {
        $customer = $this->getCustomerData();

        if ($rutField = Mage::getStoreConfig('payment/ebanx_settings/rut_field')) {
            if ($rutField === 'taxvat') {
                return $this->order->getCustomerTaxvat();
            }

            if ($customer[$rutField]) {
                return $customer[$rutField];
            }
        }

        if (!is_array($customer)
            || !array_key_exists('ebanx-document', $customer)
            || !is_array($customer['ebanx-document'])
            || !array_key_exists($methodCode, $customer['ebanx-document'])
        ) {
            return '';
        }

        return $customer['ebanx-document'][$methodCode];
    }

    /**
     * @param string $methodCode EBANX method code
     * @return mixed|string
     */
    public function getColombianDocumentNumber($methodCode)
    {
        $customer = $this->getCustomerData();

        if ($dniField = Mage::getStoreConfig('payment/ebanx_settings/dni_field')) {
            if ($dniField === 'taxvat') {
                return $this->order->getCustomerTaxvat();
            }

            if ($customer[$dniField]) {
                return $customer[$dniField];
            }
        }

        if (!is_array($customer)
            || !array_key_exists('ebanx-document', $customer)
            || !is_array($customer['ebanx-document'])
            || !array_key_exists($methodCode, $customer['ebanx-document'])
        ) {
            return '';
        }

        return $customer['ebanx-document'][$methodCode];
    }

    /**
     * @param string $methodCode EBANX method code
     * @return mixed
     */
    public function getArgetinianDocument($methodCode)
    {
        $customer = $this->getCustomerData();

        if ($cdiField = Mage::getStoreConfig('payment/ebanx_settings/cdi_field')) {
            if ($cdiField === 'taxvat') {
                return $this->order->getCustomerTaxvat();
            }

            if ($customer[$cdiField]) {
                return $customer[$cdiField];
            }
        }

        return $customer['ebanx-document'][$methodCode];
    }

    /**
     * @param string $methodCode EBANX method code
     * @return mixed
     */
    public function getPeruvianDocumentNumber($methodCode)
    {
        $customer = $this->getCustomerData();

        if ($dniField = Mage::getStoreConfig('payment/ebanx_settings/dni_field_pe')) {
            if ($dniField === 'taxvat') {
                return $this->order->getCustomerTaxvat();
            }

            if ($customer[$dniField]) {
                return $customer[$dniField];
            }
        }

        return $customer['ebanx-document'][$methodCode];
    }

    /**
     * @param string $document Document
     * @return string
     */
    public function getPersonType($document)
    {
        $document = str_replace(array('.', '-', '/'), '', $document);

        if ($this->getCustomerData()['country_id'] !== 'BR' || strlen($document) < 14) {
            return Person::TYPE_PERSONAL;
        }

        return Person::TYPE_BUSINESS;
    }

    /**
     * @param mixed $data Data to be logged
     * @return void
     */
    public function errorLog($data)
    {
        $this->log($data, 'ebanx_error');
    }

    /**
     * @param mixed  $data      Data to be logged
     * @param string $filename  Filename of the log file
     * @param string $extension Log extension
     * @return void
     */
    public function log($data, $filename = 'ebanx', $extension = '.log')
    {
        $isLogEnabled = Mage::getStoreConfig('payment/ebanx_settings/debug_log') === '1';

        if (!$isLogEnabled) {
            return;
        }

        Mage::log($data, null, $filename . $extension, true);
    }

    /**
     * @param  string $address Address to be split
     * @return array
     */
    public function splitStreet($address)
    {
        $result = preg_match('/^([^,\-\/\#0-9]*)\s*[,\-\/\#]?\s*([0-9]+)\s*[,\-\/]?\s*([^,\-\/]*)(\s*[,\-\/]?\s*)([^,\-\/]*)$/', $address, $matches);
        if ($result === false) {
            throw new \RuntimeException(sprintf('Problems trying to parse address: \'%s\'', $address));
        }
        if ($result === 0) {
            return array(
                'streetName' => $address,
                'houseNumber' => 'S/N',
                'additionToAddress' => ''
            );
        }
        $street_name = $matches[1];
        $house_number = $matches[2];
        $addition_to_address = $matches[3] . $matches[4] . $matches[5];
        if (empty($street_name)) {
            $street_name = $matches[3];
            $addition_to_address = $matches[5];
        }
        return array(
            'streetName' => $street_name,
            'houseNumber' => $house_number ?: 'S/N',
            'additionToAddress' => $addition_to_address
        );
    }

    /**
     * @param string $hash   Url Hash
     * @param string $format Voucher format
     * @return string|void
     */
    public function getVoucherUrlByHash($hash, $format = 'basic')
    {
        $res = $this->getPaymentByHash($hash);

        if ($res['status'] !== 'SUCCESS') {
            return;
        }

        $payment = $res['payment'];

        switch ($payment['payment_type_code']) {
            case 'boleto':
                $url = $payment['boleto_url'];
                break;
            case 'pagoefectivo':
                $url = $payment['cip_url'];
                break;
            case 'oxxo':
                $url = $payment['oxxo_url'];
                break;
            case 'baloto':
                $url = $payment['baloto_url'];
                break;
            case 'spei':
                $url = $payment['spei_url'];
                break;
            case 'rapipago':
                $url = $payment['voucher_url'];
                break;
            case 'pagofacil':
                $url = $payment['voucher_url'];
                break;
            case 'cupon':
                $url = $payment['voucher_url'];
                break;
            default:
                $url = '';
        }

        if ('mobile' == $format) {
            return "{$url}&target_device=mobile";
        }

        return "{$url}&format={$format}";
    }

    /**
     * @param string $hash Payment hash
     * @return mixed
     */
    public function getPaymentByHash($hash)
    {
        $ebanx = Mage::getSingleton('ebanx/api')->ebanx();

        return $ebanx->paymentInfo()->findByHash($hash);
    }

    /**
     * @param string $currency Currency
     * @param float  $value    Amount
     * @return mixed
     */
    public function getLocalAmountWithTax($currency, $value)
    {
        $ebanx = Mage::getSingleton('ebanx/api')->ebanx();

        return $ebanx->exchange()->siteToLocalWithTax($currency, $value);
    }

    /**
     * @param string $currency Currency
     * @param float  $value    Amount
     * @return mixed
     */
    public function getLocalAmountWithoutTax($currency, $value)
    {
        $ebanx = Mage::getSingleton('ebanx/api')->ebanx();

        return $ebanx->exchange()->siteToLocal($currency, $value);
    }

    /**
     * @return mixed
     */
    public function hasToShowInlineIcon()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/payment_methods_visualization');
    }

    /**
     * @param string $code EBANX Method Code
     * @return bool
     */
    public function isEbanxMethod($code)
    {
        $ebanxMethods = array(
            'ebanx_cc_br',
            'ebanx_boleto',
            'ebanx_tef',
            'ebanx_wallet',
            'ebanx_sencillito',
            'ebanx_servipag',
            'ebanx_webpay',
            'ebanx_multicaja',
            'ebanx_pse',
            'ebanx_baloto',
            'ebanx_cc_co',
            'ebanx_cc_mx',
            'ebanx_dc_mx',
            'ebanx_oxxo',
            'ebanx_spei',
            'ebanx_safetypay',
            'ebanx_pagoefectivo',
            'ebanx_cc_ar',
            'ebanx_rapipago',
            'ebanx_pagofacil',
            'ebanx_otroscupones',
            'ebanx_safetypay_ec',
        );
        return in_array($code, $ebanxMethods);
    }

    /**
     * @param string $methodCode EBANX Method Code
     * @return mixed
     */
    public function getDocumentFieldsRequiredForMethod($methodCode)
    {
        $methodsToFields = array(
            // Brazil
            'ebanx_boleto'       => array('cpf_field', 'cnpj_field'),
            'ebanx_tef'          => array('cpf_field', 'cnpj_field'),
            'ebanx_wallet'       => array('cpf_field', 'cnpj_field'),
            'ebanx_cc_br'        => array('cpf_field', 'cnpj_field'),
            // Chile
            'ebanx_sencillito'   => array(),
            'ebanx_servipag'     => array(),
            'ebanx_webpay'       => array('rut_field'),
            'ebanx_multicaja'    => array(),
            // Colombia
            'ebanx_baloto'       => array(),
            'ebanx_pse'          => array(),
            'ebanx_cc_co'        => array('dni_field'),
            // Mexico
            'ebanx_oxxo'         => array(),
            'ebanx_spei'         => array(),
            'ebanx_cc_mx'        => array(),
            'ebanx_dc_mx'        => array(),
            // Peru
            'ebanx_pagoefectivo' => array('dni_field_pe'),
            'ebanx_safetypay'    => array('dni_field_pe'),
            // Argentina
            'ebanx_cc_ar'        => array('cdi_field'),
            'ebanx_rapipago'     => array('cdi_field'),
            'ebanx_pagofacil'    => array('cdi_field'),
            'ebanx_otroscupones' => array('cdi_field'),
            // Ecuador
            'ebanx_safetypay_ec' => array(),
        );

        return $methodsToFields[$methodCode];
    }

    /**
     * @return array
     */
    public function getSandboxWarningText()
    {
        $countryCode = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->getCountry();
        $country = Mage::helper('ebanx')->transformCountryCodeToName($countryCode);

        if ($country === Country::BRAZIL) {
            return array(
                'alert' => 'Ainda estamos testando esse tipo de pagamento. Por isso, a sua compra não será cobrada nem enviada.',
                'tag' => 'EM TESTE',
            );
        }

        return array(
            'alert' => 'Todavia estamos probando este método de pago. Por eso su compra no sera cobrada ni enviada.',
            'tag' => 'EN PRUEBA',
        );
    }
}
