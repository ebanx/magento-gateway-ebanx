<?php

use Ebanx\Benjamin\Models\Country;

class Ebanx_Gateway_Model_Mexico_Debitcard extends Ebanx_Gateway_Model_Payment_Debitcard
{
    protected $_code = 'ebanx_dc_mx';

    protected $_formBlockType = 'ebanx/form_debitcard';
    protected $_infoBlockType = 'ebanx/info_debitcard';

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
     * @return string
     */
    protected function getCountry()
    {
        return Country::MEXICO;
    }
}
