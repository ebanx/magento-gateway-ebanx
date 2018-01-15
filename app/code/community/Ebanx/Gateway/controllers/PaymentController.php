<?php

class Ebanx_Ebanx_PaymentController extends Ebanx_Gateway_IndexController
{
    /**
     * Legacy notification route support
     *
     * @return void
     */
    public function notifyAction()
	{
		parent::notificationAction();
    }

    protected function loadOrder()
    {
        $this->order = $this->helper->getLegacyOrderByHash($this->hash);
    }
}
