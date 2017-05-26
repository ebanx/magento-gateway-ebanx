<?php
class Ebanx_Gateway_Helper_Data extends Mage_Core_Helper_Abstract {
    public function getMode() {
        return Mage::getStoreConfig('payment/ebanx_settings/mode');
    }

    public function isModeSandbox() {
        return $this->getMode() === Ebanx_Gateway_Model_Source_Mode::SANDBOX;
    }

    public function getIntegrationKey() {
        return Mage::getStoreConfig('payment/ebanx_settings/integration_key_' . $this->getMode());
    }

    public function getIntegrationKeySandbox() {
        return Mage::getStoreConfig('payment/ebanx_settings/integration_key_' . Ebanx_Gateway_Model_Source_Mode::SANDBOX);
    }

    public function getIntegrationKeyLive() {
        return Mage::getStoreConfig('payment/ebanx_settings/integration_key_' . Ebanx_Gateway_Model_Source_Mode::LIVE);
    }

    public function getDueDateDays() {
        return Mage::getStoreConfig('payment/ebanx_settings/due_date_days');
    }

    public function getDueDate() {
        $dueDate = new Zend_Date(Mage::getModel('core/date')->timestamp());
        return $dueDate->addDay($this->getDueDateDays());
    }
}