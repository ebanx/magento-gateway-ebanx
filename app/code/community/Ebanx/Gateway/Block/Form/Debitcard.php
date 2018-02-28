<?php
use Ebanx\Benjamin\Models\Country;

class Ebanx_Gateway_Block_Form_Debitcard extends Mage_Payment_Block_Form_Cc
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('ebanx/form/debitcard.phtml');
	}

    public function getSandboxWarningText()
    {
        $countryCode = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->getCountry();
        $country = Mage::helper('ebanx')->transformCountryCodeToName($countryCode);

        if($country === Country::BRAZIL){
            return 'Ainda estamos testando esse tipo de pagamento. Por isso, a sua compra não será cobrada nem enviada.';
        }

        return 'Todavia estamos probando este método de pago. Por eso su compra no sera cobrada ni enviada.';
    }
}
