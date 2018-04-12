<?php

class Ebanx_Gateway_Model_Lead extends Mage_Core_Model_Abstract
{
    /**
     * Ebanx_Gateway_Model_Lead constructor.
     */
    public function __construct()
    {
        $this->_init('ebanx/lead');
    }
}
