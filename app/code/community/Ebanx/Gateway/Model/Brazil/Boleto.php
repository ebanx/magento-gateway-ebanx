<?php

class Ebanx_Gateway_Model_Brazil_Boleto extends Ebanx_Gateway_Payment
{
    protected $_code = 'ebanx_boleto';

    protected $_formBlockType = 'ebanx/form_boleto';
    protected $_infoBlockType = 'ebanx/info_boleto';

    /**
     * Ebanx_Gateway_Model_Brazil_Boleto constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->gateway = $this->ebanx->boleto();
    }

    /**
     * @param null $quote unused
     *
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_brazil']));
    }

    /**
     * @return void
     */
    public function persistPayment()
    {
        parent::persistPayment();
        $this->payment->setEbanxBarCode($this->result['payment']['boleto_barcode']);
    }
}
