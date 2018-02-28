<?php

use Ebanx\Benjamin\Models\Country;

abstract class Ebanx_Gateway_Block_Form_Creditcard extends Mage_Payment_Block_Form_Cc
{
	public function getInstalmentTerms()
	{
		return $this->getMethod()->getInstalmentTerms();
	}

	public function getTotal()
	{
		return $this->getMethod()->getTotal();
	}

	public function canShowSaveCardOption()
	{
		return Mage::getSingleton('checkout/session')->getQuote()->getCheckoutMethod() == "register" || Mage::getSingleton('customer/session')->isLoggedIn();
	}

	/**
	 * @return array
	 */
	protected function getSavedCards()
	{
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
			return array();
		}
		$customerId =  Mage::getSingleton('customer/session')->getCustomer()->getId();

		return Mage::getModel('ebanx/usercard')->getCustomerSavedCards($customerId);
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

	public function formatInstalment($instalment, $localCurrency)
	{
		$amount = Mage::app()->getLocale()->currency($localCurrency)->toCurrency($instalment->localAmountWithTax);
		$instalmentNumber = $instalment->instalmentNumber;
		$interestMessage = $this->getInterestMessage($instalment->hasInterests);
		$message = sprintf('%sx de %s %s', $instalmentNumber, $amount, $interestMessage);
		return $message;
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

	/**
	 * @param bool $hasInterests
	 * @return string
	 */
	abstract protected function getInterestMessage($hasInterests);

	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate($this->getTemplatePath());
	}

	/**
	 * @return string
	 */
	abstract protected function getTemplatePath();
}
