<?php

class Ebanx_Gateway_Model_Source_Methodsargentina
{
    const CREDIT_CARD = 'ebanx_cc_ar';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::CREDIT_CARD,
                'label' => Mage::helper('ebanx')->__('Credit Card')
            ),
        );
    }
}
