<?php

class Ebanx_Gateway_Block_Info_Otroscupones extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Otroscupones
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/otroscupones.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
