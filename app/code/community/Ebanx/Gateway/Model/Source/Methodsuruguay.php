<?php

class Ebanx_Gateway_Model_Source_Methodsuruguay
{
    const CREDIT_CARD = 'ebanx_cc_uy';
    const DEBIT_CARD = 'ebanx_dc_uy';

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
            array(
                'value' => self::DEBIT_CARD,
                'label' => Mage::helper('ebanx')->__('Debit Card')
            ),
        );
    }
}
