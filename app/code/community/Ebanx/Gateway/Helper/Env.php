<?php
require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

class Ebanx_Gateway_Helper_Env extends Mage_Core_Helper_Abstract
{
    /**
     * @return bool
     */
    public function isTest()
    {
        return !!(!empty($_ENV['EBANX_TEST_MODE']));
    }
}
