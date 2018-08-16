<?php

class Ebanx_Gateway_Model_Invoice_Interest extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * @param Mage_Sales_Model_Order_Invoice $invoice
     *
     * @return Mage_Sales_Model_Order_Invoice_Total_Abstract
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $amount = $order->getEbanxInterestAmount();

        if ($amount) {
            $invoice->setGrandTotal($invoice->getGrandTotal() + $amount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $amount);
        }

        return $this;
    }
}
