<?php

class Ebanx_Gateway_Block_Catalog_Product_View_Oneclick extends Mage_Core_Block_Template
{
	/**
	 * @var array|Ebanx_Gateway_Model_Resource_Usercard_Collection
	 */
	public $usercards;
	/**
	 * @var Mage_Customer_Model_Customer
	 */
	public $customer;

	public function __construct(array $args = array())
	{
		parent::__construct($args);
		$this->initialize();
	}

	public function getText()
	{
		$country = $this->getCountry();
		$text = array(
			'local-amount' => 'Total a pagar en Peso mexicano: ',
			'cvv'          => 'Código de verificación',
			'instalments'  => 'Número de parcelas',
		);
		switch ($country) {
			case 'BR':
				$text['local-amount'] = 'Total a pagar com IOF (0.38%): ';
				$text['cvv'] = 'Código de segurança';
				break;
			case 'MX':
				break;
		}
		return $text;
	}

	public function getAddress()
	{
		$addressId = $this->customer->getDefaultShipping();
		if (!$addressId) {
			return array();
		}
		$address = Mage::getModel('customer/address')->load($addressId)->getData();

		return $address;
	}

	public function canShowOneclickButton()
	{
		return Mage::getSingleton('customer/session')->isLoggedIn()
		       && Mage::getStoreConfig('payment/ebanx_settings/one_click_payment')
			   && $this->usercards
			   && $this->usercards->getSize()
			   && $this->getAddress()['street']
			   && ($this->customer->getEbanxCustomerDocument()
				   || $this->getCountry() === 'MX');
	}

	private function initialize()
	{
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->usercards = array();
		}
		$this->customer = Mage::getSingleton('customer/session')->getCustomer();

		$this->usercards = Mage::getModel('ebanx/usercard')->getCustomerSavedCards($this->customer->getId());
	}

	/**
	 * @return string
	 */
	private function getCountry()
	{
		$address = $this->getAddress();
		if (!array_key_exists('country_id', $address)) {
			return '';
		}

		return $address['country_id'];
	}

	/**
	 * @return string
	 */
	public function getLocalCurrency()
	{
		return $this->getCountry() === 'MX' ? 'MXN' : 'BRL';
	}

	public function getLocalAmount($currency, $formatted = true)
	{
		$amount = round(Mage::helper('ebanx')->getLocalAmountWithTax($currency, $this->getTotal()), 2);

		return $formatted ? $this->formatPriceWithLocalCurrency($currency, $amount) : $amount;
	}

	private function formatPriceWithLocalCurrency($currency, $price)
	{
		return Mage::app()->getLocale()->currency($currency)->toCurrency($price);
	}

	public function getTotal()
	{
		return Mage::registry('current_product')->getPrice();
	}

	public function getInstalmentTerms()
	{
		return $this->getMethod()->getInstalmentTerms($this->getTotal());
	}

	/**
	 * @return Ebanx_Gateway_Model_Payment_Creditcard
	 */
	private function getMethod()
	{
		switch ($this->getCountry()) {
			case 'BR':
				return new Ebanx_Gateway_Model_Mexico_Creditcard();
			case 'CO':
				return new Ebanx_Gateway_Model_Colombia_Creditcard();
			case 'MX':
			default:
				return new Ebanx_Gateway_Model_Mexico_Creditcard();
		}
	}

	public function formatInstalment($instalment, $localCurrency)
	{
		$amount           = Mage::app()->getLocale()->currency($localCurrency)->toCurrency($instalment->localAmountWithTax);
		$instalmentNumber = $instalment->instalmentNumber;
		$interestMessage  = $this->getInterestMessage($instalment->hasInterests);
		$message          = sprintf('%sx de %s %s', $instalmentNumber, $amount, $interestMessage);

		return $message;
	}
}
