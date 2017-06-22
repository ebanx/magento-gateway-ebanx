<?php
class Ebanx_Gateway_Model_Quote_Total extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function __construct()
    {
        $this->setCode('ebanx_interest');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();
        $payment = $quote->getPayment();

        $baseDiscount = 10;
        if(!$payment->hasMethodInstance()) {
            Mage::log(
                'No interest - ' . $payment->getMethodInstance()->getCode(), //Objects extending Varien_Object can use this
                Zend_Log::DEBUG,  //Log level
                'total.log',         //Log file name; if blank, will use config value (system.log by default)
                true              //force logging regardless of config setting
            );

            return $this;
        }

        $payment = $payment->getMethodInstance();

//        TODO: IF CREDIT CARD
        if($payment->getCode() === 'ebanx_cc_br') {
            Mage::log(
                $payment->getGatewayFields(),
                Zend_Log::DEBUG,  //Log level
                'total.log',         //Log file name; if blank, will use config value (system.log by default)
                true              //force logging regardless of config setting
            );

//            $instalments = $payment->getInstalments();
//            $interestRate = $payment->getMethodInstance()->getInterestRate($instalments);
//            $instalmentAmount = $payment->getMethodInstance()->calcInstalmentAmount($amount, $instalments, $interestRate);
//            $amount = $instalmentAmount * $instalments;
//
////            GET INTEREST AMOUNT
////            $baseDiscount = $payment->gateway->getPaymentTermsForCountryAndValue(Country::BRAZIL, $address->getBaseGrandTotal());
//            $discount = Mage::app()->getStore()->convertPrice($baseDiscount);
//            $address->setBaseEbanxInterestAmount($baseDiscount);
//            $address->setEbanxInterestAmount($discount);
//
//            $address->setBaseGrandTotal($address->getBaseGrandTotal() + $baseDiscount);
//            $address->setGrandTotal($address->getGrandTotal() + $discount);
        }

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getEbanxInterestAmount();
        $title = Mage::helper('ebanx')->__('Interest Rate');
        if ($amount != 0){
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => $title,
                'value' => $amount,
            ));
        }
        return $this;
    }
}