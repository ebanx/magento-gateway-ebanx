<?php

class Ebanx_Gateway_Model_Observer
{
	public function interceptPaymentNotification($observer)
	{
		$request = $observer->getEvent()->getData('front')->getRequest();

		if (!$this->isEbanxPaymentRequest($request)) return;

		$helper = Mage::helper('ebanx');
		$hash   = $request->hash;
		$order  = $helper->getOrderByHash($hash);

		try {
			$api     = Mage::getSingleton('ebanx/api')->ebanx();
			$mode    = $order->getEbanxEnvironment() === 'sandbox' ? true: false;
			$payment = $api->paymentInfo()->findByHash($hash, $mode);

			$ebanxStatus = $payment['payment']['status'];

			$status = $helper->getEbanxMagentoOrder($ebanxStatus);

			// Updating the order status
			$order->setData('status', $status);

			// Adding an order note
			$order->addStatusHistoryComment(__('EBANX: The payment has been updated to: %s.', $helper->getTranslatedOrderStatus($ebanxStatus)));

			$order->save();

			Mage::log(print_r($payment, true), null, 'ebanx_order_notification.log', true);
		}
		catch (Exception $e) {
			$order->addStatusHistoryComment(__('EBANX: We could not update the order status. Error message: %s.', $e->getMessage()));

			Mage::log($e->getMessage(), null, 'ebanx_error.log', true);

			// Mage::throwException($e->getMessage());
		}
	}

	private function isEbanxPaymentRequest($request)
	{
		$hasHash = isset($request->hash) && !empty($request->hash);
		$hasPaymentCode = isset($request->merchant_payment_code) && !empty($request->merchant_payment_code);
		$hasPaymentTypeCode = isset($request->payment_type_code) && !empty($request->payment_type_code);

		return $hasHash && $hasPaymentCode && $hasPaymentTypeCode;
	}
}
