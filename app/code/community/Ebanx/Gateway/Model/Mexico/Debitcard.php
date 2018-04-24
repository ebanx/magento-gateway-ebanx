<?php

class Ebanx_Gateway_Model_Mexico_Debitcard extends Ebanx_Gateway_Payment
{
    protected $_code = 'ebanx_dc_mx';

    protected $_formBlockType = 'ebanx/form_debitcard';
    protected $_infoBlockType = 'ebanx/info_debitcard';

    /**
     * Ebanx_Gateway_Model_Mexico_Debitcard constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->gateway = $this->ebanx->debitCard();
    }

    /**
     * @param null $quote unused
     *
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_mexico']));
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
