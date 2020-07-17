<?php

abstract class Ebanx_Gateway_Model_Payment_Debitcard extends Ebanx_Gateway_Payment
{
    /**
     * Ebanx_Gateway_Model_Payment_Debitcard constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->gateway = $this->ebanx->debitCard();
    }

    /**
     * @return string
     */
    abstract protected function getCountry();

    /**
     * @param string $country 2 letter ISO country
     *
     * @return bool
     */
    public function canUseForCountry($country)
    {
        return $this->helper->transformCountryCodeToName($country) === $this->getCountry()
            && parent::canUseForCountry($country);
    }


    /**
     * @return void
     */
    public function setupData()
    {
        parent::setupData();

        $this->data->setGatewayFields(Mage::app()->getRequest()->getPost('payment'));
        $this->data->setPaymentType('dc');
    }

    /**
     * @return void
     */
    public function transformPaymentData()
    {
        $this->paymentData = $this->adapter->transformCard($this->data);
    }
}
