<?php

class Ebanx_Gateway_Block_Adminhtml_Sales_Order_Creditmemo_Totals extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals
{
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $amount = $this->getOrder()->getEbanxInterestAmount();

        if ($amount) {
            $this->addTotalBefore(new Varien_Object(array(
                'code'      => 'ebanx_interest',
                'value'     => $amount,
                'base_value'=> $amount / $this->getOrder()->getBaseToOrderRate(),
                'label'     => $this->helper('ebanx')->__('Interest Amount'),
            ), array('tax')));
        }

        return $this;
    }
}
