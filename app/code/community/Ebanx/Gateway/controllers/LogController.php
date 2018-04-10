<?php

class Ebanx_Gateway_LogController extends Mage_Core_Controller_Front_Action
{
    public function fetchAction()
    {
        header('Content-Type: application/json');

        $integration_key = $this->getRequest()->getParam('integration_key');

        if (empty($integration_key) || $integration_key !== Mage::helper('ebanx/data')->getIntegrationKey()) {
            die(json_encode([]));
        }

        $res = Ebanx_Gateway_Log_Logger::fetch();

        Ebanx_Gateway_Log_Logger::truncate();

        echo json_encode($res);
    }
}
