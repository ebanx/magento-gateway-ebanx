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
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(Plugincheck::getPlugincheckList()));
    }
}
