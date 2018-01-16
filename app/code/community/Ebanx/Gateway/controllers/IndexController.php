<?php

class Ebanx_Gateway_IndexController extends Mage_Core_Controller_Front_Action
{
	public function notificationAction()
	{
		$this->_redirect('*/payment/notify', $this->getRequest()->getParams());
	}
}
