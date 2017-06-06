<?php
class Ebanx_Gateway_Block_Form_CreditCardBR extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/form/creditcard_br.phtml');
    }
}
