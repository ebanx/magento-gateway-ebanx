<?php

class Ebanx_Gateway_Model_Observer
{
	public function interceptPaymentNotification($observer)
	{
		$request = $observer->getEvent()->getData('front')->getRequest();

		if (!$this->isEbanxPaymentRequest($request)) return;

		// TOOD: Make a Benjamin query to find the payment by hash
		$payment = [
			'payment' => [
				'status' => 'CO'
			]
		];

		$hash = $request->hash;
		$helper = Mage::helper('ebanx');
		$order = $helper->getOrderByHash($hash);

		Mage::log(print_r($order, true), null, 'observer.log', true);
	}

	private function isEbanxPaymentRequest($request)
	{
		$hasHash = isset($request->hash) && !empty($request->hash);
		$hasPaymentCode = isset($request->merchant_payment_code) && !empty($request->merchant_payment_code);
		$hasPaymentTypeCode = isset($request->payment_type_code) && !empty($request->payment_type_code);

		return $hasHash && $hasPaymentCode && $hasPaymentTypeCode;
	}
}
