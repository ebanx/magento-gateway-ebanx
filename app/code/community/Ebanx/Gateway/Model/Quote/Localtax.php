<?php

class Ebanx_Gateway_Model_Quote_Localtax extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Ebanx_Gateway_Model_Quote_Localtax constructor.
     */
    public function __construct()
    {
        $this->setCode('ebanx_local_tax');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address adress
     *
     * @return void
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        if ($address->getAddressType() !== Mage_Sales_Model_Quote_Address::TYPE_BILLING) {
            return;
        }

        $isBrazilLocalAmount = Mage::app()->getStore()->getCurrentCurrencyCode() === 'BRL';
        if (!$isBrazilLocalAmount || Mage::getStoreConfig('payment/ebanx_settings/iof_local_amount') === '0') {
            return;
        }

        $payment = $address->getQuote()->getPayment();

        if (!$payment->hasMethodInstance() || Mage::app()->getRequest()->getActionName() !== 'savePayment') {
            return;
        }

        $gatewayFields = Mage::app()->getRequest()->getPost('payment');
        $grandTotal = $gatewayFields['grand_total'];

        if ($grandTotal <= 0) {
            return;
        }

        $grandTotalWithInterest = Mage::getModel('checkout/session')->getQuote()->getData('grand_total');
        $localTaxAmount = $grandTotalWithInterest * 0.0038;
        $address->setEbanxLocalTaxAmount($localTaxAmount);
        $address->setGrandTotal($address->getGrandTotal() + $localTaxAmount);
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address address
     *
     * @return $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getEbanxLocalTaxAmount();
        $title  = Mage::helper('ebanx')->__('IOF');
        if ($amount != 0) {
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => $title,
                'value' => $amount,
            ));
        }
        return $this;
    }
}
