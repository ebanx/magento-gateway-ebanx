<?php
class Ebanx_Gateway_Model_Source_Mode {
    const MODE_SANDBOX = 'sandbox';
    const MODE_LIVE = 'live';

    public function toOptionArray() {
        return array(
            array(
                'value' => self::MODE_SANDBOX,
                'label' => Mage::helper('ebanx')->__('Sandbox')
            ),
            array(
                'value' => self::MODE_LIVE,
                'label' => Mage::helper('ebanx')->__('Live')
            ),
        );
    }
}
