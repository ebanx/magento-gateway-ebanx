<?php

class Ebanx_Gateway_Model_Brazil_Tef extends Ebanx_Gateway_Payment
{
    protected $_code = 'ebanx_tef';

    protected $_formBlockType = 'ebanx/form_tef';
    protected $_infoBlockType = 'ebanx/info_tef';

    /**
     * Ebanx_Gateway_Model_Brazil_Tef constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->gateway = $this->ebanx->tef();
    }

    /**
     * @return void
     */
    public function transformPaymentData()
    {
        parent::transformPaymentData();

        $bank = Mage::app()->getRequest()->getPost('ebanx_tef');
        $bankCode = Mage::helper('ebanx')->transformTefToBankName($bank);

        $this->paymentData->bankCode = $bankCode;
    }

    /**
     * @param null $quote unused
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_brazil']));
    }
}
