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

	public function __construct(array $args = [])
	{
		parent::__construct($args);
		$this->initialize();
	}

	public function getText()
	{
		return [
			'local-amount' => 'Total a pagar com IOF (0.38%): ',
			'cvv'          => 'Código de segurança',
			'instalments'  => 'Número de parcelas',
		];
	}

	public function getAddress()
	{
		$addressId = $this->customer->getDefaultShipping();
		if (!$addressId) {
			return '';
		}
		$address = Mage::getModel('customer/address')->load($addressId)->getData();

		return $address;
	}

	public function canShowOneclickButton()
	{
		return Mage::getSingleton('customer/session')->isLoggedIn()
			   && $this->usercards
			   && $this->usercards->getSize()
			   && $this->getAddress()['street']
			   && ($this->customer->getEbanxCustomerDocument()
				   || $this->getCountry() === 'MX');
	}

	private function initialize()
	{
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->usercards = [];
		}
		$this->customer = Mage::getSingleton('customer/session')->getCustomer();

		$this->usercards = Mage::getModel('ebanx/usercard')->getCustomerSavedCards($this->customer->getId());
	}

	/**
	 * @return string
	 */
	private function getCountry()
	{
		return $this->getAddress()['country_id'];
	}

	/**
	 * @return string
	 */
	public function getLocalCurrency()
	{
		return $this->getAddress()['country_id'] === 'MX' ? 'MXN' : 'BRL';
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
		return $this->getAddress()['country_id'] === 'MX' ? new Ebanx_Gateway_Model_Mexico_Creditcard() : new Ebanx_Gateway_Model_Brazil_Creditcard();
	}

	public function formatInstalment($instalment)
	{
		$amount = Mage::helper('core')->formatPrice($instalment->baseAmount, false);
		$instalmentNumber = $instalment->instalmentNumber;
		$interestMessage = $this->getInterestMessage($instalment->hasInterests);
		$message = sprintf('%sx de %s %s', $instalmentNumber, $amount, $interestMessage);
		return $message;
	}
}
