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
			'cvv' => 'Código de segurança',
			'instalments' => 'Número de parcelas',
		];
	}

	public function getAddress()
	{
		$addressId = $this->customer->getDefaultShipping();
		if (!$addressId){
			return '';
		}
		$address = Mage::getModel('customer/address')->load($addressId)->getData();
		return $address['street'];
	}

	public function canShowOneclickButton()
	{
		return Mage::getSingleton('customer/session')->isLoggedIn()
				&& $this->usercards
				&& $this->usercards->getSize()
				&& $this->getAddress()
				&& $this->customer->getEbanxCustomerDocument();
	}

	private function initialize()
	{
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->usercards = [];
		}
		$this->customer =  Mage::getSingleton('customer/session')->getCustomer();

		$this->usercards = Mage::getModel('ebanx/usercard')->getCustomerSavedCards($this->customer->getId());
	}
}
