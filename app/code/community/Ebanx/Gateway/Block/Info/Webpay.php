<?php

class Ebanx_Gateway_Block_Info_Webpay extends Ebanx_Gateway_Block_Info_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/info/webpay.phtml');
	}
}