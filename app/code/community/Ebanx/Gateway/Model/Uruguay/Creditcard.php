<?php
use Ebanx\Benjamin\Models\Country;

class Ebanx_Gateway_Model_Uruguay_Creditcard extends Ebanx_Gateway_Model_Payment_Creditcard
{
    protected $_code = 'ebanx_cc_uy';

    protected $_formBlockType = 'ebanx/form_creditcard_uy';
    protected $_infoBlockType = 'ebanx/info_creditcarduy';

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
