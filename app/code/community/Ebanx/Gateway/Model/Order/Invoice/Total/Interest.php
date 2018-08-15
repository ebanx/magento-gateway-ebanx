<?php
class Ebanx_Gateway_Model_Order_Invoice_Total_Interest extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order=$invoice->getOrder();
        $orderDepositTotal = $order->getEbanxInterestAmount();
        if ($orderDepositTotal&&count($order->getInvoiceCollection())==0) {
            $invoice->setGrandTotal($invoice->getGrandTotal()+$orderDepositTotal);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()+$orderDepositTotal);
        }
        return $this;
    }
}
