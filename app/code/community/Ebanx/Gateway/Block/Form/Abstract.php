<?php

abstract class Ebanx_Gateway_Block_Form_Abstract extends Mage_Payment_Block_Form
{
    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->getMethod()->getTotal();
    }

    /**
     * @param string $currency Currency type
     * @param float  $price    Amount
     * @return mixed
     */
    private function formatPriceWithLocalCurrency($currency, $price)
    {
        return Mage::app()->getLocale()->currency($currency)->toCurrency($price);
    }

    /**
     * @param string $currency  Currency type
     * @param bool   $formatted Format the amount
     * @return float|mixed
     */
    public function getLocalAmount($currency, $formatted = true)
    {
        $amount = round(Mage::helper('ebanx')->getLocalAmountWithTax($currency, $this->getTotal()), 2);

        return $formatted ? $this->formatPriceWithLocalCurrency($currency, $amount) : $amount;
    }

    /**
     * @param string $currency  Currency type
     * @param bool   $formatted Format the amount
     * @return float|mixed
     */
    public function getLocalAmountWithoutTax($currency, $formatted = true)
    {
        $amount = round(Mage::helper('ebanx')->getLocalAmountWithoutTax($currency, $this->getTotal()), 2);

        return $formatted ? $this->formatPriceWithLocalCurrency($currency, $amount) : $amount;
    }
}
