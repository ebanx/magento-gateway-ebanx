<?php

/**
 * @deprecated 2.4.0
 */
class Ebanx_Gateway_IndexController extends Mage_Core_Controller_Front_Action
{
	public function notificationAction()
	{
		$this->getResponse()->setRedirect(
			Mage::getUrl(
				'*/payment/notify',
				$this->getRequest()->getParams()
			),
			301
		);
	}
}
