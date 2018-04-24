<?php

class Ebanx_Gateway_Helper_Order extends Ebanx_Gateway_Helper_Data
{
    /**
     * @param string $hash Hash of the payment
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrderByHash($hash)
    {
        return $this->getOrderBy('ebanx_payment_hash', $hash);
    }

    /**
     * @param string $hash Hash of the payment
     *
     * @return Mage_Sales_Model_Order
     */
    public function getLegacyOrderByHash($hash)
    {
        return $this->getOrderBy('ebanx_hash', $hash);
    }

    /**
     * @param string $ebanxStatus Payment status
     *
     * @return string
     */
    public function getEbanxMagentoOrder($ebanxStatus)
    {
        $status = array(
            'CO' => Mage::getStoreConfig('payment/ebanx_settings/payment_co_status'),
            'PE' => Mage::getStoreConfig('payment/ebanx_settings/payment_pe_status'),
            'OP' => Mage::getStoreConfig('payment/ebanx_settings/payment_op_status'),
            'CA' => Mage::getStoreConfig('payment/ebanx_settings/payment_ca_status')
        );

        return $status[strtoupper($ebanxStatus)];
    }

    /**
     * @param string $ebanxStatus Payment status
     *
     * @return string
     */
    public function getTranslatedOrderStatus($ebanxStatus)
    {
        $status = array(
            'CO' => 'Confirmed',
            'PE' => 'Pending',
            'OP' => 'Open',
            'CA' => 'Canceled'
        );

        return $status[strtoupper($ebanxStatus)];
    }

    /**
     * @param string $field Field to filter
     * @param string $value Value to filter
     *
     * @return Mage_Sales_Model_Order
     * @throws Ebanx_Gateway_Exception
     */
    private function getOrderBy($field, $value)
    {
        $model = Mage::getModel('sales/order_payment')
            ->getCollection()
            ->setPageSize(1)
            ->setCurPage(1)
            ->addFieldToFilter($field, $value)
            ->load();

        if ($model->count() !== 1) {
            throw new Ebanx_Gateway_Exception($this->__('EBANX: Invalid payment hash. We couldn\'t find the order.'));
        };

        $payment = $model->getFirstItem();

        return Mage::getModel('sales/order')->load($payment->getParentId());
    }
}
