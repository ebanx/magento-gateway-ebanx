<?php
class Ebanx_Gateway_Block_Form_CreditCardBR extends Mage_Payment_Block_Form_Cc
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ebanx/form/creditcard_br.phtml');
    }

    public function getInstalmentTerms()
    {
        return $this->getMethod()->getInstalmentTerms();
    }

    public function formatInstalment($instalment)
	{
		$amount = Mage::helper('core')->formatPrice($instalment->baseAmount, false);
		$instalmentNumber = $instalment->instalmentNumber;
		$interestMessage = $instalment->hasInterests ? ' com juros' : '';
		$message = sprintf('%sx de %s%s', $instalmentNumber, $amount, $interestMessage);
		return $message;
	}
}
