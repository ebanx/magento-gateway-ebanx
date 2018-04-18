<?php

class Ebanx_Gateway_Model_Quote_Interest extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    /**
     * Ebanx_Gateway_Model_Quote_Interest constructor.
     */
    public function __construct()
    {
        $this->setCode('ebanx_interest');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address address
     *
     * @return void
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        if ($address->getAddressType() !== Mage_Sales_Model_Quote_Address::TYPE_BILLING) {
            return;
        }

        $payment = $address->getQuote()->getPayment();

        if (!$payment->hasMethodInstance() || Mage::app()->getRequest()->getActionName() !== 'savePayment') {
            return;
        }

        $isCardPayment = substr($payment->getMethodInstance()->getCode(), 0, 8) === 'ebanx_cc';
        if (!$isCardPayment) {
            return;
        }

        $paymentInstance = $payment->getMethodInstance();

        $gatewayFields = Mage::app()->getRequest()->getPost('payment');
        if (!array_key_exists('instalments', $gatewayFields)) {
            return;
        }
        $instalments = $gatewayFields['instalments'];
        $grandTotal = $gatewayFields['grand_total'];
        $instalmentTerms = $paymentInstance->getInstalmentTerms($grandTotal);

        if (!array_key_exists($instalments - 1, $instalmentTerms)) {
            return;
        }

        $grandTotal = $grandTotal ?: $instalmentTerms[0]->baseAmount;
        $instalmentAmount = $instalmentTerms[$instalments - 1]->baseAmount;
        $interestAmount = ($instalmentAmount * $instalments) - $grandTotal;

        if ($interestAmount > 0) {
            $address->setEbanxInterestAmount($interestAmount);
            $address->setGrandTotal($address->getGrandTotal() + $interestAmount);
        }
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address address
     *
     * @return $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getEbanxInterestAmount();
        $title = Mage::helper('ebanx')->__('Interest Amount');
        if ($amount != 0) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => $title,
                'value' => $amount,
            ));
        }
        return $this;
    }
}
