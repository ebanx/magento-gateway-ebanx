<?php

class Ebanx_Gateway_Block_Checkout_Success_Cashpayment extends Ebanx_Gateway_Block_Checkout_Success_Payment
{
    protected $_order;

    /**
     * @param string $format Date format
     * @return mixed
     */
    public function getEbanxDueDate($format = 'dd/MM')
    {
        $date = new Zend_Date($this->getPayment()->getEbanxDueDate());

        return $date->get($format);
    }

    /**
     * @return mixed
     */
    public function getEbanxUrlPrint()
    {
        $hash = $this->getEbanxPaymentHash();
        return $this->helper->getVoucherUrlByHash($hash, 'print');
    }

    /**
     * @return mixed
     */
    public function getEbanxPaymentHash()
    {
        return $this->getOrder()->getPayment()->getEbanxPaymentHash();
    }

    /**
     * @return mixed
     */
    public function getEbanxUrlPdf()
    {
        $hash = $this->getEbanxPaymentHash();
        return $this->helper->getVoucherUrlByHash($hash, 'pdf');
    }

    /**
     * @return mixed
     */
    public function getEbanxUrlBasic()
    {
        $hash = $this->getEbanxPaymentHash();
        return $this->helper->getVoucherUrlByHash($hash, 'basic');
    }

    /**
     * @return mixed
     */
    public function getVoucherUrl()
    {
        return Mage::getUrl('ebanx/voucher', array(
            'hash' => $this->getEbanxPaymentHash()
        ));
    }

    /**
     * @return mixed
     */
    public function getEbanxUrlMobile()
    {
        $hash = $this->getEbanxPaymentHash();
        return $this->helper->getVoucherUrlByHash($hash, 'mobile');
    }

    /**
     * @return Ebanx_Gateway_Block_Checkout_Success_Cashpayment
     */
    protected function _construct()
    {
        parent::_construct();
    }
}
