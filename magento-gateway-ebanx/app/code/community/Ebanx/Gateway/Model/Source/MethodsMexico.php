<?php
class Ebanx_Gateway_Model_Source_MethodsMexico {
    const CREDIT_CARD = 'credit_card';
    const DEBIT_CARD = 'debit_card';
    const OXXO = 'oxxo';

    public function toOptionArray() {
        return array(
            array(
                'value' => self::CREDIT_CARD,
                'label' => Mage::helper('ebanx')->__('Credit Card')
            ),
            array(
                'value' => self::DEBIT_CARD,
                'label' => Mage::helper('ebanx')->__('Debit Card')
            ),
            array(
                'value' => self::OXXO,
                'label' => Mage::helper('ebanx')->__('OXXO')
            ),
        );
    }
}
