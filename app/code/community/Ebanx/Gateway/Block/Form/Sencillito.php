<?php

class Ebanx_Gateway_Block_Form_Sencillito extends Ebanx_Gateway_Block_Form_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Form_Sencillito
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/form/sencillito.phtml');
    }
}
