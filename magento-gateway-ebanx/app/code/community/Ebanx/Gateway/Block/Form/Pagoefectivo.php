<?php
class Ebanx_Gateway_Block_Form_Pagoefectivo extends Mage_Payment_Block_Form
{
    protected function _construct()
	{
        parent::_construct();
        $this->setTemplate('ebanx/form/pagoefectivo.phtml');
    }
}
