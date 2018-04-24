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

    /**
     * @return void
     */
    public function notifyAction()
    {
        try {
            Ebanx_Gateway_Log_Logger_NotificationReceived::persist(array(
                'params' => $this->getRequest()->getParams()
            ));

            $this->initialize();
        } catch (Ebanx_Gateway_Exception $e) {
            $this->helper->errorLog($e->getMessage());

            $response = array(
                'success' => false,
                'message' => 'Error - ' . $e->getMessage(),
            );

            return $this->setResponseToJson($response, 400);
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

    /**
     * @return void
     */
    private function loadOrder()
    {
        try {
            $this->order = $this->helper->getOrderByHash($this->hash);
        } catch (Ebanx_Gateway_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            // LEGACY: Support for legacy orders which store their hash somewhere else
            $this->order = $this->helper->getLegacyOrderByHash($this->hash);
        }
    }

    /**
     * @throws Ebanx_Gateway_Exception
     * @return void
     */
    private function initialize()
    {
        $this->helper = Mage::helper('ebanx/order');
        $this->validateEbanxPaymentRequest();
        $this->hash = $this->getRequest()->getParam('hash_codes');
        $this->loadOrder();
        $this->statusEbanx = $this->loadEbanxPaymentStatus();
        $this->validateStatus();
    }

    /**
     * @throws Ebanx_Gateway_Exception
     * @return void
     */
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

    /**
     * @param object $order Order
     *
     * @return string
     */
    private function _getPaymentMethod($order)
    {
        return $order->getPayment()->getMethodInstance()->getCode();
    }

    /**
     * @param array $data JSON array
     * @param int $statusCode
     *
     * @return void
     */
    private function setResponseToJson($data, $statusCode = 200)
    {
        $this->getResponse()->clearHeaders()->setHeader(
            'Content-type',
            'application/json'
        )->setHeader('HTTP/1.1', $statusCode, true);

        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode($data)
        );
    }

    /**
     * @return string
     * @throws Ebanx_Gateway_Exception
     */
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

    /**
     * @return string
     */
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

    /**
     * @param string $statusEbanx Ebanx status
     *
     * @return void
     */
    private function updateOrder($statusEbanx)
    {
        $statusMagento = $this->helper->getEbanxMagentoOrder($statusEbanx);

        $this->order->setData('status', $statusMagento);
        $this->order->setState($this->ebanxStatusToState[$statusEbanx]);
        $this->order->addStatusHistoryComment($this->helper->__('EBANX: The payment has been updated to: %s.', $this->helper->getTranslatedOrderStatus($statusEbanx)));
        $this->order->save();
    }

    /**
     * @return void
     */
    private function createInvoice()
    {

        $invoice = $this->order->prepareInvoice();
        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
        $invoice->setTransactionId($this->order->getPayment()->getEbanxPaymentHash());
        $invoice->register()->pay();
        Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save();
    }

    /**
     * @throws Ebanx_Gateway_Exception
     * @return void
     */
    private function validateStatus()
    {
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
