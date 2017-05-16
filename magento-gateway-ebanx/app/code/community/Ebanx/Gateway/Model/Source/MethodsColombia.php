<?php
class Ebanx_Gateway_Model_Source_MethodsColombia {
    const EFT = 'eft';
    const BALOTO = 'baloto';

    public function toOptionArray() {
        return array(
            array(
                'value' => self::EFT,
                'label' => Mage::helper('gateway')->__('PSE - Pago Seguros en Líne (EFT)')
            ),
            array(
                'value' => self::BALOTO,
                'label' => Mage::helper('gateway')->__('Baloto')
            ),
        );
    }
}
