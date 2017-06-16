<?php
class Ebanx_Gateway_Block_Form_CreditCardBR extends Mage_Payment_Block_Form_Cc
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/form/creditcard_br.phtml');
    }

    public function getInstalmentOptions()
    {
        return $this->getMethod()->getInstalmentOptions();
    }
}
