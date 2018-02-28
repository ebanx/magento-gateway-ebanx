<?php

use Ebanx\Benjamin\Models\Country;

abstract class Ebanx_Gateway_Block_Form_Abstract extends Mage_Payment_Block_Form
{
	public function getTotal()
	{
		return $this->getMethod()->getTotal();
	}

	private function formatPriceWithLocalCurrency($currency, $price)
	{
		return Mage::app()->getLocale()->currency($currency)->toCurrency($price);
	}

	public function getLocalAmount($currency, $formatted = true)
	{
		$amount = round(Mage::helper('ebanx')->getLocalAmountWithTax($currency, $this->getTotal()), 2);

		return $formatted ? $this->formatPriceWithLocalCurrency($currency, $amount) : $amount;
	}

	public function getLocalAmountWithoutTax($currency, $formatted = true)
	{
		$amount = round(Mage::helper('ebanx')->getLocalAmountWithoutTax($currency, $this->getTotal()), 2);

		return $formatted ? $this->formatPriceWithLocalCurrency($currency, $amount) : $amount;
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
