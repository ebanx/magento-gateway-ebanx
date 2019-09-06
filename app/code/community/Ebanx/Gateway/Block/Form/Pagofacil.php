<?php

class Ebanx_Gateway_Block_Form_Pagofacil extends Ebanx_Gateway_Block_Form_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Form_Pagofacil
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/form/pagofacil.phtml');
    }
}
