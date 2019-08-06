<?php

class Ebanx_Gateway_Block_Form_Otroscupones extends Ebanx_Gateway_Block_Form_Abstract
{
    /**
     * @return Ebanx_Gateway_Block_Form_Otroscupones
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/form/otroscupones.phtml');
    }
}
