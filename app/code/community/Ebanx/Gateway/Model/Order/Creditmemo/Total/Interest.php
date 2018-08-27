<?php

class Ebanx_Gateway_Model_Order_Creditmemo_Total_Interest extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     *
     * @return Mage_Sales_Model_Order_Creditmemo_Total_Abstract
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $interestAmount = $order->getEbanxInterestAmount();

        if ($interestAmount && count($order->getCreditmemosCollection()) === 0) {
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $interestAmount);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $interestAmount);
        }

        return $this;
    }
}
