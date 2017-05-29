<?php
class Ebanx_Gateway_Model_Baloto extends Mage_Payment_Model_Method_Abstract {
    protected $_code = 'ebanx_baloto';

    protected $_formBlockType = 'ebanx/form_baloto';
    protected $_infoBlockType = 'ebanx/info_baloto';

    protected $_isGateway = true;
    protected $_canUseForMultishipping = false;
    protected $_isInitializeNeeded = true;

    public function validate() {
        parent::validate();
        return $this;
    }
    
    public function assignData($data) {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        $info = $this->getInfoInstance();
        
        
        return $this;
    }

    public function initialize($paymentAction, $stateObject) {

        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();

        $dueDate = Mage::helper('ebanx')->getDueDate();
        
        // create data to benjamin
        $data = new Varien_Object();
        $data->setMerchantPaymentCode($order->getIncrementId())
            ->setEbanxMethod('baloto')
            ->setEbanxDueDate($dueDate);


        // connect api
        $result = Mage::getSingleton('ebanx/api')->createCashPayment($data);
        Mage::log($result, null, 'benjamin-result.log', true);

        // throw errors
        
        // save order attributes
        $payment->setEbanxPaymentHash($result['payment']['hash'])
            ->setEbanxDueDate($dueDate)
            ->setEbanxBarCode($result['payment']['baloto_barcode']);

        return $this;
    }
}
