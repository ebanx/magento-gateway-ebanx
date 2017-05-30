<?php

class Ebanx_Gateway_Model_Chile_Sencillito extends Ebanx_Gateway_Model_Payment
{
	protected $_code = 'ebanx_sencillito';
	
	protected $_formBlockType = 'ebanx/form_sencillito';
	protected $_infoBlockType = 'ebanx/info_sencillito';
	
	public function initialize($paymentAction, $stateObject)
	{
		$payment = $this->getInfoInstance();
        $order = $payment->getOrder();

		$data = new Varien_Object();
		$data->setMerchantPaymentCode($order->getIncrementId())
			->setEbanxMethod('sencillito');

		Mage::log($data, null, 'ebanx-sencillito.log');
	}
	
}
