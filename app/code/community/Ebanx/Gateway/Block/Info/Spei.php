<?php

class Ebanx_Gateway_Block_Info_Spei extends Ebanx_Gateway_Block_Info_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Info_Spei
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/info/spei.phtml');
        if ($this->isAdmin()) {
            $this->setTemplate('ebanx/info/default.phtml');
        }
    }
}
