<?php
class Ebanx_Gateway_Model_Source_MethodsChile {
    const SENCILLITO = 'sencillito';
    const SERVIPAG = 'servipag';

    public function toOptionArray() {
        return array(
            array(
                'value' => self::SENCILLITO,
                'label' => Mage::helper('gateway')->__('Sencillito')
            ),
            array(
                'value' => self::SERVIPAG,
                'label' => Mage::helper('gateway')->__('Servipag')
            ),
        );
    }
}
