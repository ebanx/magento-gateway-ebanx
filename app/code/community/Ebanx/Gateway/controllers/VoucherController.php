<?php

class Ebanx_Gateway_VoucherController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$hash = $this->getRequest()->getParam('hash');

		$url = Mage::helper('ebanx')->getVoucherUrlByHash($hash);

		if (!in_array('curl', get_loaded_extensions())) {
			$this->getResponse()
			     ->setHeader('Content-Type', 'text/html')
			     ->setBody(file_get_contents($url));
		}
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$html = curl_exec($curl);
		if (curl_error($curl)) {
			return;
		}
		curl_close($curl);
		$this->getResponse()
		     ->setHeader('Content-Type', 'text/html')
		     ->setBody($html);
	}
}
