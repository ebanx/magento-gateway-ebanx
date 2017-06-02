<?php
class Ebanx_Gateway_Block_Checkout_Success extends Mage_Checkout_Block_Onepage_Success {
    public function getSuccessPaymentBlock() {
        $orderId = Mage::app()->getRequest()->getParam('order_id');
        $order = Mage::getModel("sales/order")->load($orderId);

        return $order->getPayment()->getMethodInstance()->getCode();
    }
}