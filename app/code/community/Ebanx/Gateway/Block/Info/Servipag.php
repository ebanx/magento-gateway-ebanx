<?php

class Ebanx_Gateway_Block_Info_Servipag extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Servipag
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/servipag.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
