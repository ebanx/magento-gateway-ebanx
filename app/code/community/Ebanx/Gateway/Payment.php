<?php

abstract class Ebanx_Gateway_Payment extends Mage_Payment_Model_Method_Abstract
{
    // phpcs:disable
    static protected $redirect_url;

    protected $adapter;
    protected $configs;
    protected $data;
    protected $ebanx;
    protected $gateway;
    protected $helper;
    protected $order;
    protected $payment;
    protected $paymentData;
    protected $result;

    protected $_isGateway = true;
    protected $_canUseFormMultishipping = false;
    protected $_isInitializeNeeded = true;
    protected $_canRefund = true;

    /**
     * Ebanx_Gateway_Payment constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->configs = Mage::getStoreConfig('payment/ebanx_settings');
        $this->ebanx = Mage::getSingleton('ebanx/api')->ebanx();
        $this->adapter = Mage::getModel('ebanx/adapters_paymentadapter');
        $this->helper = Mage::helper('ebanx');
    }

    /**
     * @param string $paymentAction action performed
     * @param object $stateObject   state object
     *
     * @return void
     */
    public function initialize($paymentAction, $stateObject)
    {
        try {
            $this->payment = $this->getInfoInstance();
            $this->order = $this->payment->getOrder();
            $this->setupData();

            $this->transformPaymentData();

            $this->processPayment();

            $this->persistPayment();

            parent::initialize($paymentAction, $stateObject);
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function setupData()
    {
        // Create payment data
        $id = $this->payment->getOrder()->getIncrementId();
        $time = time();
        $env = Mage::getSingleton('ebanx/api')->getConfig()->isSandbox ? 'SB' : 'PD';
        $merchantPaymentCode = "{$env}-{$id}-{$time}";

        $this->data = new Varien_Object();
        $this->data
            ->setMerchantPaymentCode($merchantPaymentCode)
            ->setOrderId($id)
            ->setDueDate($this->helper->getDueDate())
            ->setEbanxMethod($this->getCode())
            ->setAmountTotal($this->order->getGrandTotal())
            ->setItems($this->order->getAllVisibleItems())
            ->setRemoteIp($this->order->getRemoteIp())
            ->setBillingAddress($this->order->getBillingAddress())
            ->setOrder($this->order);
    }

    /**
     * @return void
     */
    public function transformPaymentData()
    {
        $this->paymentData = $this->adapter->transform($this->data);
    }

    /**
     * @return void
     * @throws Mage_Core_Exception Exception.
     */
    public function processPayment()
    {
        $res = $this->gateway->create($this->paymentData);
        $error = Mage::helper('ebanx/error');

	    $this->persistPaymentRequestResponse($res);

	    $this->throwExceptionIfPaymentUnsuccessful($res, $error);
	    $this->throwExceptionIfPaymentCancelled($res, $error);

	    $this->order->setEmailSent(true);
        $this->order->sendNewOrderEmail();

	    $this->setRedirectUrl($res);

	    $this->result = $res;
    }

    /**
     * @param string $devError
     * @param string $liveError
     *
     * @return string
     */
    private static function resolveProcessPaymentErrorMessage($devError, $liveError)
    {
        return Mage::getIsDeveloperMode() ? $devError : $liveError;
    }
    /**
     * @return void
     */
    public function persistPayment()
    {
        $this->payment
            ->setEbanxPaymentHash($this->result['payment']['hash'])
            ->setEbanxEnvironment($this->helper->getMode())
            ->setEbanxDueDate($this->helper->getDueDate($this->order->getCreatedAt()))
            ->setEbanxLocalAmount($this->result['payment']['amount_br']);

        if ($this->order->getCustomerId()) {
            $documentNumber = $this->helper->getDocumentNumber($this->order, $this->data->getEbanxMethod());
            $customer = Mage::getModel('customer/customer')->load($this->order->getCustomerId())
                ->setEbanxCustomerDocument($documentNumber);

            $methodCode = $this->order->getPayment()->getMethodInstance()->getCode();
            $documentFields = $this->helper->getDocumentFieldsRequiredForMethod($methodCode);
            foreach ($documentFields as $field) {
                $fieldValue = Mage::getStoreConfig('payment/ebanx_settings/' . $field) ?: 'taxvat';
                $customer->setData($fieldValue, $documentNumber);
            }
            $customer->save();
        }
    }

    /**
     * @param Varien_Object $payment payment object
     * @param float         $amount  money amount to refund
     *
     * @return $this
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $hash = $payment->getEbanxPaymentHash();
        $result = $this->ebanx->refund()->requestByHash($hash, $amount, $this->helper->__('Refund requested by Magento Admin Panel'));

	    $this->persistRefundRequest($amount, $hash, $result);

	    $this->throwExceptionIfRefundErrored($result);

	    return $this;
    }

    /**
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return self::$redirect_url;
    }

    /**
     * @param null $quote not used
     *
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return Mage::getStoreConfig('payment/ebanx_settings/enabled')
            && $this->helper->areKeysFilled();
    }

    /**
     * @param string $country 2 letter ISO country
     *
     * @return bool
     */
    public function canUseForCountry($country)
    {
        $countryName = $this->helper->transformCountryCodeToName($country);

        return $this->gateway->isAvailableForCountry($countryName);
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        $quote = $this->getInfoInstance()->getQuote();
        if (!$quote) {
            return $this->getInfoInstance()->getOrder()->getPayment()->getEbanxLocalAmount();
        }
        return $quote->getGrandTotal();
    }

	/**
	 * @param $res
	 * @param $error
	 * @return mixed
	 */
	private function throwExceptionIfPaymentUnsuccessful($res, $error) {
		if ($res['status'] !== 'SUCCESS') {
			$country = $this->order->getBillingAddress()->getCountry();
			$code = $res['status_code'];

			$this->helper->errorLog($res);
			Mage::throwException($error->getError($code, $country) . " ($code)");
		}
	}

	/**
	 * @param $res
	 * @param $error
	 * @return mixed
	 */
	private function throwExceptionIfPaymentCancelled($res, $error) {
		if ($res['payment']['status'] === 'CA') {
			$country = $this->order->getBillingAddress()->getCountry();

			$errorType = isset($res['payment']['transaction_status'])
				? 'GENERAL'
				: 'CC-' . $res['payment']['transaction_status']['code'];

			Mage::throwException(
				self::resolveProcessPaymentErrorMessage(
					$res['payment']['hash'] . '-' . $res['payment']['merchant_payment_code'],
					$error->getError($errorType, $country)
				)
			);
		}
	}

	/**
	 * @param $res
	 * @return mixed
	 */
	private function setRedirectUrl($res) {
		self::$redirect_url = !empty($res['redirect_url'])
			? $res['redirect_url']
			: Mage::getUrl('checkout/onepage/success');
	}

	/**
	 * @param $res
	 */
	private function persistPaymentRequestResponse($res) {
		Ebanx_Gateway_Log_Logger_Checkout::persist(array(
			'config' => Mage::getSingleton('ebanx/api')->getConfig(),
			'request' => $this->paymentData,
			'response' => $res
		));
	}

	 /* @param $amount
	 * @param $hash
	 * @param $result
	 */
	private function persistRefundRequest($amount, $hash, $result) {
		Ebanx_Gateway_Log_Logger_Refund::persist(array(
			'request' => array(
				'hash' => $hash,
				'amount' => $amount,
				'description' => $this->helper->__('Refund requested by Magento Admin Panel')
			),
			'response' => $result
		));
	}

	/**
	 * @param $result
	 */
	private function throwExceptionIfRefundErrored($result) {
		if ($result['status'] === 'ERROR') {
			$errorMsg = $this->helper->__('Error processing refund: ' . $result['status_message'] . ' (' . $result['status_code'] . ')');
			Mage::throwException($errorMsg);
		}
	}
}
