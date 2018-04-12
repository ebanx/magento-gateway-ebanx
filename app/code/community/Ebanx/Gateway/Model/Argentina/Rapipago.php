<?php

class Ebanx_Gateway_Model_Argentina_Rapipago extends Ebanx_Gateway_Payment
{
    protected $_code = 'ebanx_rapipago';

    protected $_formBlockType = 'ebanx/form_rapipago';
    protected $_infoBlockType = 'ebanx/info_rapipago';

    /**
     * Ebanx_Gateway_Model_Argentina_Rapipago constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->gateway = $this->ebanx->rapipago();
    }

    /**
     * @param null $quote unused
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_argentina']));
    }
}
