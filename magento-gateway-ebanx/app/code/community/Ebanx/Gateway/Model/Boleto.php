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

    public function initialize() {

    //     // connect api

    //     // throw errors

    //     // set token

        return $this;
    }
}
