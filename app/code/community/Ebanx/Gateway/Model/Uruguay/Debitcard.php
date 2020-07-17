<?php

use Ebanx\Benjamin\Models\Country;

class Ebanx_Gateway_Model_Uruguay_Debitcard extends Ebanx_Gateway_Model_Payment_Debitcard
{
    protected $_code = 'ebanx_dc_uy';

    protected $_formBlockType = 'ebanx/form_debitcard_uy';
    protected $_infoBlockType = 'ebanx/info_debitcarduy';

    /**
     * @param null $quote unused
     *
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return parent::isAvailable() && in_array($this->getCode(), explode(',', $this->configs['payment_methods_uruguay']));
    }

    /**
     * @return string
     */
    protected function getCountry()
    {
        return Country::URUGUAY;
    }
}
