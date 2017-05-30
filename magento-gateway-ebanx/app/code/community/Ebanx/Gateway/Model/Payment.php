<?php

abstract class Ebanx_Gateway_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
	protected $_isGateway = true;
	protected $_canUseFormMultishipping = false;
	protected $_isInitializeNeeded = true;
	
	public function validate() {
        parent::validate();
        return $this;
    }

	public function assignData($data)
	{
		if (!($data instanceof Varien_Object)) {
			$data = new Varien_Object($data);
		}
		
		$info = $this->getInfoInstance();
		
		
		return $this;
	}

	function initialize($paymentAction, $stateObject) {
		throw new Exception('You need to create this method.');
	}
}