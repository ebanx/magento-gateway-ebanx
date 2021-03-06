<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

use Ebanx\Benjamin\Models\Bank;
use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Person;

class Ebanx_Gateway_Helper_Data extends Mage_Core_Helper_Abstract
{
    const URL_PRINT_LIVE = 'https://print.ebanxpay.com/';
    const URL_PRINT_SANDBOX = 'https://sandbox.ebanxpay.com/print/';

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
     * @return string
     */
    public function getMode()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/mode');
    }

    /**
     * @return string
     */
    public function getSandboxIntegrationKey()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/integration_key_' . Ebanx_Gateway_Model_Source_Mode::SANDBOX);
    }

    /**
     * @return string
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
     * @return string
     */
    public function getIntegrationKey()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/integration_key_' . $this->getMode());
    }

    /**
     * @return string
     */
    public function getPublicIntegrationKey()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/integration_key_public_' . $this->getMode());
    }

    /**
     * @param string $date   Date if not currently
     * @param string $format Format desired to the date
     *
     * @return Zend_Date
     */
    public function getDueDate($date = null, $format = 'YYYY-MM-dd HH:mm:ss')
    {
        $date = !is_null($date) ? $date : Mage::getModel('core/date')->timestamp();
        $dueDate = new Zend_Date($date);

        return $dueDate->addDay($this->getDueDateDays())->get($format);
    }

    /**
     * @return string
     */
    public function getDueDateDays()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/due_date_days');
    }

    /**
     * @return string
     */
    public function getMaxInstalments()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/max_instalments');
    }

    /**
     * @return string
     */
    public function getMinInstalmentValue()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/min_instalment_value');
    }

    /**
     * @return string
     */
    public function getInterestRate()
    {
        return Mage::helper('core/unserializeArray')->unserialize(Mage::getStoreConfig('payment/ebanx_settings/interest_rate'));
    }

    /**
     * @return string
     */
    public function saveCreditCardAllowed()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/save_card_data');
    }

    /**
     * @param string $bankCode Code of the bank
     *
     * @return string
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
     *
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
     *
     * @return string
     */
    public function getLabelForComplianceField($code)
    {
        switch ($code) {
            case 'ebanx_boleto':
            case 'ebanx_tef':
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

            case 'ebanx_cc_uy':
            case 'ebanx_dc_uy':
                return $this->__('CI Document');

            default:
                return $this->__('Document Number');
        }
    }

    /**
     * @param string $countryCode Country string
     *
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
            case 'uy':
                return $this->__('CI Document');
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
     * @param object $order      Some order
     * @param string $methodCode Method code
     *
     * @return null|string
     */
    public function getDocumentNumber($order, $methodCode)
    {
        $this->order = $order;
        $countryCode = $this->getCustomerData()['country_id'];
        $country = $this->transformCountryCodeToName($countryCode);

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
            case Country::URUGUAY:
                return $this->getUruguayanDocumentNumber($methodCode);
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
     *
     * @return bool|string
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
     * @param string $methodCode EBANX method code
     *
     * @return string
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
     *
     * @return string
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
     *
     * @return string
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
     *
     * @return string
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
     *
     * @return string
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
     * @param string $methodCode EBANX method code
     *
     * @return string
     */
    public function getUruguayanDocumentNumber($methodCode)
    {
        $customer = $this->getCustomerData();

        if ($ciField = Mage::getStoreConfig('payment/ebanx_settings/ci_field')) {
            if ($ciField === 'taxvat') {
                return $this->order->getCustomerTaxvat();
            }

            if ($customer[$ciField]) {
                return $customer[$ciField];
            }
        }

        return $customer['ebanx-document'][$methodCode];
    }

    /**
     * @param string $document Document
     *
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
     * @param array|string $data Data to be logged
     *
     * @return void
     */
    public function errorLog($data)
    {
        $this->log($data, 'ebanx_error');
    }

    /**
     * @param array|string $data      Data to be logged
     * @param string       $filename  Filename of the log file
     * @param string       $extension Log extension
     *
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
     * @param string $address Address to be split
     *
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
            if (empty($matches[3])) {
                $street_name = $matches[0];
            } else {
                $street_name = $matches[3];
            }

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
     *
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
     *
     * @return array
     */
    public function getPaymentByHash($hash)
    {
        $ebanx = Mage::getSingleton('ebanx/api')->ebanx();

        return $ebanx->paymentInfo()->findByHash($hash);
    }

    /**
     * @param string $currency Currency
     * @param float  $value    Amount
     *
     * @return double
     */
    public function getLocalAmountWithTax($currency, $value)
    {
        $ebanx = Mage::getSingleton('ebanx/api')->ebanx();

        return $ebanx->exchange()->siteToLocalWithTax($currency, $value);
    }

    /**
     * @param string $currency Currency
     * @param float  $value    Amount
     *
     * @return double
     */
    public function getLocalAmountWithoutTax($currency, $value)
    {
        $ebanx = Mage::getSingleton('ebanx/api')->ebanx();

        return $ebanx->exchange()->siteToLocal($currency, $value);
    }

    /**
     * @return string
     */
    public function hasToShowInlineIcon()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/payment_methods_visualization');
    }

    /**
     * @param string $code EBANX Method Code
     *
     * @return bool
     */
    public function isEbanxMethod($code)
    {
        $ebanxMethods = array(
            'ebanx_cc_br',
            'ebanx_boleto',
            'ebanx_tef',
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
            'ebanx_cc_uy',
            'ebanx_dc_uy',
        );
        return in_array($code, $ebanxMethods);
    }

    /**
     * @param string $methodCode EBANX Method Code
     *
     * @return array
     */
    public function getDocumentFieldsRequiredForMethod($methodCode)
    {
        $methodsToFields = array(
            // Brazil
            'ebanx_boleto'       => array('cpf_field', 'cnpj_field'),
            'ebanx_tef'          => array('cpf_field', 'cnpj_field'),
            'ebanx_cc_br'        => array('cpf_field', 'cnpj_field'),
            // Chile
            'ebanx_sencillito'   => array('rut_field'),
            'ebanx_servipag'     => array('rut_field'),
            'ebanx_webpay'       => array('rut_field'),
            'ebanx_multicaja'    => array('rut_field'),
            // Colombia
            'ebanx_baloto'       => array('dni_field'),
            'ebanx_pse'          => array('dni_field'),
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
            // Uruguay
            'ebanx_cc_uy'        => array('ci_field'),
            'ebanx_dc_uy'        => array('ci_field'),
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

    /**
     * @param string $country
     *
     * @return array
     */
    public function getDocumentTypesByCountry($country)
    {
        $documentTypes = array();

        if ($country === 'AR') {
            $documentTypes = array('ARG_CUIT' => 'CUIT', 'ARG_CUIL' => 'CUIL', 'ARG_CDI' => 'CDI', 'ARG_DNI' => 'DNI');
        } else if ($country === 'CO') {
            $documentTypes = array('COL_CC' => 'Cédula de Ciudadania', 'COL_NIT' => 'NIT', 'COL_CE' => 'Cédula de Exntrajeria');
        }

        return $documentTypes;
    }
}
