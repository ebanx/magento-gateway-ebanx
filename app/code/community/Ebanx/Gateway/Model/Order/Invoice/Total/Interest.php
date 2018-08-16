<?php
class Ebanx_Gateway_Model_Order_Invoice_Total_Interest extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $interestAmount = $order->getEbanxInterestAmount();

        if ($interestAmount && count($order->getInvoiceCollection()) === 0) {
            $invoice->setGrandTotal($invoice->getGrandTotal() + $interestAmount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $interestAmount);
        }

        return $this;
    }
}
