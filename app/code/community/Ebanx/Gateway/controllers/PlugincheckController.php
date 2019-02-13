<?php


class Ebanx_Gateway_PlugincheckController extends Mage_Core_Controller_Front_Action
{
    /**
    * Render Plugincheck info
    * @return void
    */
    public function indexAction()
    {
        Mage::getSingleton('ebanx/Plugincheck');
        echo json_encode(Plugincheck::getPlugincheckList());
    }
}
