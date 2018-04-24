<?php

abstract class Ebanx_Gateway_Block_Info_Abstract extends Mage_Payment_Block_Info
{
    /**
     * @return float
     */
    private function getTotal()
    {
        return $this->getMethod()->getTotal();
    }

    /**
     * @param string $currency Currency type
     * @param float  $price    Amount
     *
     * @return string
     */
    private function formatPriceWithLocalCurrency($currency, $price)
    {
        return Mage::app()->getLocale()->currency($currency)->toCurrency($price);
    }

    /**
     * @param string $currency  Currency type
     * @param bool   $formatted Format Amount
     *
     * @return float
     */
    public function getLocalAmount($currency, $formatted = true)
    {
        $amount = round(Mage::helper('ebanx')->getLocalAmountWithTax($currency, $this->getTotal()), 2);

        if ($this->shouldntShowIof()) {
            $amount = round(Mage::helper('ebanx')->getLocalAmountWithoutTax($currency, $this->getTotal()), 2);
        }

        return $formatted ? $this->formatPriceWithLocalCurrency($currency, $amount) : $amount;
    }

    /**
     * @param string $currency  Currency type
     * @param bool   $formatted Format Amount
     *
     * @return string|float
     */
    public function getLocalAmountWithoutTax($currency, $formatted = true)
    {
        return $formatted ? $this->formatPriceWithLocalCurrency($currency, $this->getTotal()) : $this->getTotal();
    }

    /**
     * @return bool
     */
    public function shouldntShowIof()
    {
        return Mage::getStoreConfig('payment/ebanx_settings/iof_local_amount') === '0';
    }

    /**
     * @return bool
     */
    protected function isAdmin()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return true;
        }

        if (Mage::getDesign()->getArea() == 'adminhtml') {
            return true;
        }

        return false;
    }

    /**
     * @param string $hash Payment hash
     *
     * @return string
     */
    protected function getDashboardUrl($hash)
    {
        return sprintf(
            'https://dashboard.ebanx.com%s/payments/?hash=%s',
            $this->getInfo()->getEbanxEnvironment() === 'sandbox'
                ? '/test'
                : '',
            $hash
        );
    }

    /**
     * @param string $hash Payment hash
     *
     * @return string
     */
    protected function getNotificationUrl($hash)
    {
        return $this->getUrl(
            'ebanx/payment/notify',
            array(
                'hash_codes' => $hash,
                'operation' => 'update',
                'notification_type' => 'forced',
            )
        );
    }
}
