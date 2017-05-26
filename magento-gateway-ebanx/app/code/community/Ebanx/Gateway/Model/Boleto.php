<?php
class Ebanx_Gateway_Model_Boleto extends Mage_Payment_Model_Method_Abstract {
    protected $_code = 'ebanx_boleto';

    protected $_formBlockType = 'ebanx/form_boleto';
    protected $_infoBlockType = 'ebanx/info_boleto';

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
        $data->setMerchantPaymentCode($order->getIncrementId());
        $data->setEbanxDueDate($dueDate);


        // connect api
        $result = Mage::getSingleton('ebanx/api')->createBoleto($data);
        Mage::log($result, null, 'benjamin-result.log', true);

        // throw errors
        
        // save order attributes
        $payment->setEbanxPaymentHash($result['payment']['hash'])
            ->setEbanxDueDate($dueDate)
            ->setEbanxBarCode($result['payment']['boleto_barcode']);

        return $this;
    }
}
