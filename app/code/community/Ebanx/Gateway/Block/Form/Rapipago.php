<?php

class Ebanx_Gateway_Block_Form_Rapipago extends Ebanx_Gateway_Block_Form_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Form_Rapipago
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/form/rapipago.phtml');
    }
}
