<?php

class Ebanx_Gateway_LogController extends Mage_Core_Controller_Front_Action
{
	public function fetchAction()
	{

		$integration_key = $this->getRequest()->getParam('integration_key');

		if (empty($integration_key) || ($integration_key !== Mage::helper('ebanx/data')->getSandboxIntegrationKey() && $integration_key !== Mage::helper('ebanx/data')->getLiveIntegrationKey())) {
			$this->norouteAction();

			return;
		}

		list($col, $res) = Ebanx_Gateway_Log_Logger::fetch($integration_key);

		Ebanx_Gateway_Log_Logger::delete($col);

		$this->getResponse()
		     ->setHeader('Content-Type', 'application/json')
		     ->setBody(json_encode($res));
	}
}
