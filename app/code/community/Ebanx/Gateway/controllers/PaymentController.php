<?php

class Ebanx_Gateway_PaymentController extends Mage_Core_Controller_Front_Action
{
	private $helper;
	private $order;
	private $hash;

	private $statusEbanx;
	private $ebanxStatusToState = array(
		'CO' => Mage_Sales_Model_Order::STATE_PROCESSING,
		'PE' => Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
		'CA' => Mage_Sales_Model_Order::STATE_CANCELED,
	);

	public function notifyAction()
	{
		try {
			Ebanx_Gateway_Log_Logger_NotificationReceived::persist(array(
				'params' => $this->getRequest()->getParams()
			));

			$this->initialize();
		} catch (Ebanx_Gateway_Exception $e) {
			$this->helper->errorLog($e->getMessage());

			return $this->setResponseToJson(array(
				'success' => false,
				'message' => $e->getMessage()
			));
		}

		try {
			$this->updateOrder($this->statusEbanx);

			if (Mage::helper('ebanx')->isEbanxMethod($this->_getPaymentMethod($this->order))
				&& Mage::getStoreConfig('payment/ebanx_settings/create_invoice')
				&& strtoupper($this->statusEbanx) === 'CO'
				&& $this->order->canInvoice()) {
				$this->createInvoice();
			}

			$this->setResponseToJson(array(
				'success' => true,
				'order' => $this->order->getIncrementId()
			));
		} catch (Exception $e) {
			$this->order->addStatusHistoryComment($this->helper->__('EBANX: We could not update the order status. Error message: %s.', $e->getMessage()));
			$this->helper->errorLog($e->getMessage());
			Mage::throwException(get_class($e).': '.$e->getMessage());
		}
	}

	private function loadOrder()
	{
		try {
			$this->order = $this->helper->getOrderByHash($this->hash);
		} catch (Exception $e) {
			// LEGACY: Support for legacy orders which store their hash somewhere else
			$this->order = $this->helper->getLegacyOrderByHash($this->hash);
		}
	}

	private function initialize()
	{
		$this->helper = Mage::helper('ebanx/order');
		$this->validateEbanxPaymentRequest();
		$this->hash = $this->getRequest()->getParam('hash_codes');
		$this->loadOrder();
		$this->statusEbanx = $this->loadEbanxPaymentStatus();
		$this->validateStatus();
	}

	private function validateEbanxPaymentRequest()
	{
		$request = $this->getRequest();
		$operation = $request->getParam('operation');
		$notification_type = $request->getParam('notification_type');
		$hash_codes = $request->getParam('hash_codes');

		if (empty($operation)) {
			throw new Ebanx_Gateway_Exception($this->helper->__('EBANX: Invalid operation parameter.'));
		}

		if (empty($notification_type)) {
			throw new Ebanx_Gateway_Exception($this->helper->__('EBANX: Invalid notification type parameter.'));
		}

		if (empty($hash_codes)) {
			throw new Ebanx_Gateway_Exception($this->helper->__('EBANX: Invalid hash parameter.'));
		}
	}

	private function _getPaymentMethod($order)
	{
		return $order->getPayment()->getMethodInstance()->getCode();
	}

	private function setResponseToJson($data)
	{
		$this->getResponse()->clearHeaders()->setHeader(
			'Content-type',
			'application/json'
		);

		$this->getResponse()->setBody(
			Mage::helper('core')->jsonEncode($data)
		);
	}

	private function loadEbanxPaymentStatus()
	{
		$api = Mage::getSingleton('ebanx/api')->ebanx();
		$isSandbox = $this->loadOrderEnv() === 'sandbox';
		$payment = $api->paymentInfo()->findByHash($this->hash, $isSandbox);

		Ebanx_Gateway_Log_Logger_NotificationQuery::persist(array(
			'params' => $this->getRequest()->getParams(),
			'payment' => $payment,
			'isSandbox' => $isSandbox
		));

		$this->helper->log($payment, 'ebanx_payment_notification');

		if ($payment['status'] !== 'SUCCESS') {
			throw new Ebanx_Gateway_Exception($this->helper->__('EBANX: Payment doesn\'t exist. ' . ($isSandbox ? 'sand' : 'live')));
		}

		return $payment['payment']['status'];
	}

	private function loadOrderEnv()
	{
		$env = $this->order->getPayment()->getEbanxEnvironment();

		// LEGACY: Fallback in case of legacy orders that don't save environment
		if (!$env) {
			$env = Mage::getSingleton('ebanx/api')->getConfig()->isSandbox ? 'sandbox' : 'live';
		}

		return $env;
	}

	// Actions

	private function updateOrder($statusEbanx)
	{
		$statusMagento = $this->helper->getEbanxMagentoOrder($statusEbanx);

		$this->order->setData('status', $statusMagento);
		$this->order->setState($this->ebanxStatusToState[$statusEbanx]);
		$this->order->addStatusHistoryComment($this->helper->__('EBANX: The payment has been updated to: %s.', $this->helper->getTranslatedOrderStatus($statusEbanx)));
		$this->order->save();
	}

	private function createInvoice() {

		$invoice = $this->order->prepareInvoice();
		$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
		$invoice->setTransactionId($this->order->getPayment()->getEbanxPaymentHash());
		$invoice->register()->pay();
		Mage::getModel('core/resource_transaction')
			->addObject($invoice)
			->addObject($invoice->getOrder())
			->save();
	}

	private function validateStatus() {
		if (array_key_exists($this->statusEbanx, $this->ebanxStatusToState)) {
			return;
		}

		throw new Ebanx_Gateway_Exception(
			$this->helper->__(
				'EBANX: Invalid payment status: %s.',
				$this->statusEbanx
			)
		);
	}
}
