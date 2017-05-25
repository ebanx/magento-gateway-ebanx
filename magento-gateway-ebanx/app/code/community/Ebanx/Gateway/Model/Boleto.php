<?php
class Ebanx_Gateway_Model_Boleto extends Mage_Payment_Model_Method_Abstract {
    protected $_code = 'ebanx_boleto';

    protected $_formBlockType = 'ebanx/form_boleto';
    protected $_infoBlockType = 'ebanx/info_boleto';

    protected $_isGateway = true;
    protected $_canUseForMultishipping = false;
    protected $_isInitializeNeeded = true;

    public function validate() {
        // Validate Country (canUseForCountry)
        return $this;
    }

    public function initialize($paymentAction, $stateObject) {

        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();

        // create data to benjamin
        $data = new Varien_Object();
        $data->setMerchantPaymentCode($order->getIncrementId());

        // connect api
        $result = Mage::getSingleton('ebanx/api')->createBoleto($data);

        // throw errors
        
        // save order attributes
        $payment->setEbanxPaymentHash($result['payment']['hash']);

        return $this;
    }
}
