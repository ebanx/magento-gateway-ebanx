<?php

class Ebanx_Gateway_Block_Form_Pagoefectivo extends Ebanx_Gateway_Block_Form_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Form_Pagoefectivo
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/form/pagoefectivo.phtml');
    }
}
