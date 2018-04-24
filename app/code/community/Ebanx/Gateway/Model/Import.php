<?php

class Ebanx_Gateway_Model_Import extends Mage_Directory_Model_Currency_Import_Abstract
{
    protected $_messages = array();
    /**
     * @var \Ebanx\Benjamin\Facade
     */
    protected $ebanx;

    /**
     * Retrieve rate
     *
     * @param string $currencyFrom original currency
     * @param string $currencyTo   destination currency
     *
     * @return float|null
     */
    protected function _convert($currencyFrom, $currencyTo)
    {
        $this->ebanx = Mage::getSingleton('ebanx/api')->ebanx();
        $rate = $this->ebanx->exchange()->fetchRate($currencyFrom, $currencyTo);

        return $rate ?: null;
    }
}
