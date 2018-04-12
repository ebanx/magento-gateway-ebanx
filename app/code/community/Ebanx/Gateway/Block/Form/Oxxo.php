<?php

class Ebanx_Gateway_Block_Form_Oxxo extends Ebanx_Gateway_Block_Form_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Form_Oxxo
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/form/oxxo.phtml');
    }
}
