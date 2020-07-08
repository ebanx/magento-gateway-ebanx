<?php

class Ebanx_Gateway_Model_Source_Methodsuruguay
{
    const CREDIT_CARD = 'ebanx_cc_uy';

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
