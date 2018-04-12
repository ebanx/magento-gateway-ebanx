<?php

abstract class Ebanx_Gateway_Model_Payment_Safetypay extends Ebanx_Gateway_Payment
{
    /**
     * @return string
     */
    abstract protected function getCountry();

    /**
     * Ebanx_Gateway_Model_Payment_Safetypay constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->gateway = $this->ebanx->safetyPayCash();
    }

    /**
     * @param string $paymentAction payment action
     * @param object $stateObject   state object
     *
     * @return void
     */
    public function initialize($paymentAction, $stateObject)
    {
        $safetyPayType = Mage::app()->getRequest()->getPost()['ebanx_safetypay_type'];

        $this->gateway = $this->ebanx->{'safetyPay' . $safetyPayType}();

        parent::initialize($paymentAction, $stateObject);
    }

    /**
     * @param string $country 2 letter ISO country
     * @return bool
     */
    public function canUseForCountry($country)
    {
        return $this->helper->transformCountryCodeToName($country) === $this->getCountry()
               && parent::canUseForCountry($country);
    }
}
