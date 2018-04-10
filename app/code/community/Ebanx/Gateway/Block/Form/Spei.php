<?php

class Ebanx_Gateway_Block_Form_Spei extends Ebanx_Gateway_Block_Form_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Form_Spei
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/form/spei.phtml');
    }
}
