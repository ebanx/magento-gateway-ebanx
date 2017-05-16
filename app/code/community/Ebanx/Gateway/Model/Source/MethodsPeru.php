<?php
class Ebanx_Gateway_Model_Source_MethodsPeru {
    const SAFETYPAY = 'safetypay';
    const PAGOEFECTIVO = 'pagoefectivo';

    public function toOptionArray() {
        return array(
            array(
                'value' => self::SAFETYPAY,
                'label' => Mage::helper('gateway')->__('SafetyPay')
            ),
            array(
                'value' => self::PAGOEFECTIVO,
                'label' => Mage::helper('gateway')->__('PagoEfectivo')
            ),
        );
    }
}
