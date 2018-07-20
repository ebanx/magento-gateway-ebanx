<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

class Ebanx_Gateway_Helper_Env extends Mage_Core_Helper_Abstract {
    public function isTest() {
        return !!(!empty(getenv('EBANX_TEST_MODE')));
    }
}
