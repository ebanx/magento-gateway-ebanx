<?php

class Ebanx_Gateway_Model_Capture
{
	/**
	 * @var \Ebanx\Benjamin\Facade
	 */
	private $ebanx;
	private $payment;

	public function capture_payment($observer) {
		$this->ebanx = Mage::getSingleton('ebanx/api')->ebanx();
		$this->payment = $observer
			->getEvent()
			->getInvoice()
			->getOrder()
			->getPayment();
		$hash = $this->payment->getEbanxPaymentHash();
		$method_code = $this->payment->getMethodInstance()->getCode();

		if (!isset($this->payment)
		    || !isset($hash)
		    || strpos($method_code, "ebanx_cc") === false
		    || !$this->isPaymentPending($hash)) {
			return;
		}

		$this->ebanx->creditCard()->captureByHash($hash);
	}

	private function isPaymentPending($hash)
	{
		$helper = Mage::helper('ebanx/order');
		$isSandbox = $this->payment->getEbanxEnvironment() === 'sandbox';
		$payment = $this->ebanx->paymentInfo()->findByHash($hash, $isSandbox);

		if ($payment['status'] !== 'SUCCESS') {
			throw new Ebanx_Gateway_Exception($helper->__('EBANX: Payment doesn\'t exist.'));
		}

		return $payment['payment']['status']  === 'PE';
	}
}
