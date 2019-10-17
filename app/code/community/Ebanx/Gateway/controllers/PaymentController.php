<?php

class Ebanx_Gateway_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Ebanx_Gateway_Helper_Order
     */
    private $helper;

    /**
     * @var Mage_Sales_Model_Order
     */
    private $order;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var array
     */
    private $paymentInfoEbanx;

    /**
     * @var string
     */
    private $statusEbanx;

    /**
     * @var string
     */
    private $notificationType;

    private $ebanxStatusToState = array(
        'CO' => Mage_Sales_Model_Order::STATE_PROCESSING,
        'PE' => Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
        'CA' => Mage_Sales_Model_Order::STATE_CANCELED,
    );

    private $refundStatusMap = array(
        'RE' => 'Requested',
        'CA' => 'Cancelled',
        'PE' => 'Pending',
        'CO' => 'Confirmed',
    );

    /**
     * @return void
     * @throws Mage_Core_Exception Throws the exception after logging it.
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

            $this->setResponseToJson($response, 400);

            return;
        } catch (Ebanx_Gateway_Loadcanceledcardpaymentexception $loadException) {
            $this->helper->errorLog($loadException->getMessage());

            $response = array(
                'success' => false,
                'message' => $loadException->getMessage(),
            );

            $this->setResponseToJson($response, 200);

            return;
        }

        try {
            $this->updateOrder($this->statusEbanx, $this->notificationType);

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

            if ($e->getMessage() === 'Trying to roll status back') {
                return;
            }

            Mage::throwException(get_class($e) . ': ' . $e->getMessage());
        }
    }

    /**
     * @return void
     * @throws Ebanx_Gateway_Exception                        When payment is not found.
     * @throws Ebanx_Gateway_Loadcanceledcardpaymentexception When CA CC payment
     */
    private function loadOrder()
    {
        try {
            $this->order = $this->helper->getOrderByHash($this->hash);
        } catch (Ebanx_Gateway_Exception $e) {
            $merchantPaymentCode = $this->getRequest()->getParam('merchant_payment_code');
            $isSandbox = substr($merchantPaymentCode, 0, 2) === "SB" ? true : false;
            $api = Mage::getSingleton('ebanx/api')->ebanx();
            $queryResponse = $api->paymentInfo()->findByHash($this->hash, $isSandbox);

            if ($queryResponse['status'] === 'SUCCESS'
                && $queryResponse['payment']['status'] === 'CA'
                && $this->isCreditCardPayment($queryResponse['payment']['payment_type_code'])) {
                throw new Ebanx_Gateway_Loadcanceledcardpaymentexception($this->helper->__('Declined Credit Card payment.'));
            }

            throw $e;
        }
    }

    /**
     * @return void
     * @throws Ebanx_Gateway_Exception
     * @throws Ebanx_Gateway_Loadcanceledcardpaymentexception When CA card payment not found
     */
    private function initialize()
    {
        $this->helper = Mage::helper('ebanx/order');
        $this->validateEbanxPaymentRequest();
        $this->hash = $this->getRequest()->getParam('hash_codes');
        $this->notificationType = $this->getRequest()->getParam('notification_type');
        $this->loadOrder();
        $this->paymentInfoEbanx = $this->loadEbanxPaymentInfo();
        $this->statusEbanx = $this->paymentInfoEbanx['status'];
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
     * @param array $data       JSON array
     * @param int   $statusCode HTTP status code to be returned
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
    private function loadEbanxPaymentInfo()
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

        return $payment['payment'];
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
     * @param string $statusEbanx      Ebanx status
     * @param string $notificationType Nofitication Type
     *
     * @return void
     * @throws Ebanx_Gateway_Exception Warns the payment status won't roll back.
     * @throws Exception
     */
    private function updateOrder($statusEbanx, $notificationType)
    {
        $refundCount = $this->order->getCreditmemosCollection() ? $this->order->getCreditmemosCollection()->count() : 0;

        if ($refundCount > 0) {
            $response    = array(
                'success' => false,
                'failReason' => 'Refunded payment',
                'refundCountNumber' => $refundCount,
                'currentOrderStatus' => $this->order->getData('status'),
                'ebanxStatus' => $statusEbanx,
            );

            $this->setResponseToJson($response);

            throw new Ebanx_Gateway_Exception('Trying to roll status back');
        };

        switch ($this->order->getData('status')) {
            case Mage_Sales_Model_Order::STATE_COMPLETE:
            case Mage_Sales_Model_Order::STATE_CLOSED:
            case Mage_Sales_Model_Order::STATE_CANCELED:
                $response = array(
                    'success' => false,
                    'failReason' => 'No status roll back',
                    'currentOrderStatus' => $this->order->getData('status'),
                );

                $this->setResponseToJson($response);

                throw new Ebanx_Gateway_Exception('Trying to roll status back');
        }

        $statusMagento = $this->helper->getEbanxMagentoOrder($statusEbanx);

        if ($notificationType === 'update') {
            $this->order->setData('status', $statusMagento);
            $this->order->setState($this->ebanxStatusToState[$statusEbanx]);
        }

        $this->order->addStatusHistoryComment($this->resolveHistoryComment($statusEbanx, $notificationType));
        $this->order->save();
    }

    /**
     * @return string
     */
    private function resolveHistoryComment()
    {
        if ($this->notificationType === 'update') {
            return $this->helper->__('EBANX: The payment has been updated to: %s.', $this->helper->getTranslatedOrderStatus($this->statusEbanx));
        }

        if ($this->notificationType === 'refund') {
            $refunds = $this->formatEbanxRefundData();
            return 'EBANX: a refund was issued for this payment. Refund history: <br>' . $refunds;
        }

        return $this->helper->__('EBANX: a %s was issued for this payment', $this->notificationType);
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

    /**
     * @param string $paymentTypeCode
     *
     * @return bool
     */
    private function isCreditCardPayment($paymentTypeCode)
    {
        $creditCardBrands = array(
            'elo',
            'amex',
            'aura',
            'visa',
            'carnet',
            'diners',
            'discover',
            'hipercard',
            'mastercard',
        );

        return in_array($paymentTypeCode, $creditCardBrands);
    }

    /**
     * @return string
     */
    private function formatEbanxRefundData()
    {
        $refunds = '';
        foreach ($this->paymentInfoEbanx['refunds'] as $refund) {
            $refunds .= '[ ';
            $refunds .= $refund['merchant_refund_code'] ? '<b>Merchant refund code: </b>' . $refund['merchant_refund_code'] . ', ' : '';
            $refunds .= '<b>Refund Status: </b>' . $this->refundStatusMap[$refund['status']] . ', ';
            $refunds .= '<b>Requested Date: </b> (UTC) ' . $refund['request_date'] . ', ';
            $refunds .= '<b>Refund Amount: </b>' . $refund['amount_ext'] . ', ';
            $refunds .= '<b>Description: </b>' . $refund['description'];
            $refunds .= ' ] <br>';
        }
        return $refunds;
    }
}
