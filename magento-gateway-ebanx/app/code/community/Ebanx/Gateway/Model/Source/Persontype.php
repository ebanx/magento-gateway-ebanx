<?php
class Ebanx_Gateway_Model_Source_Persontype {
    const CPF = 'cpf';
    const CNPJ = 'cnpj';

    public function toOptionArray() {
        return array(
            array(
                'value' => self::CPF,
                'label' => Mage::helper('gateway')->__('CPF - Individuals')
            ),
            array(
                'value' => self::CNPJ,
                'label' => Mage::helper('gateway')->__('CNPJ - Companies')
            ),
        );
    }
}
