<?php
class Ebanx_Gateway_Block_Catalog_Product_View_Oneclick extends Mage_Core_Block_Template
{
	/**
	 * @var array|Ebanx_Gateway_Model_Resource_Usercard_Collection
	 */
	public $usercards;

	public function __construct(array $args = [])
	{
		parent::__construct($args);
		$this->initialize();
	}

	public function canShowOneclickButton()
	{
		return Mage::getSingleton('customer/session')->isLoggedIn()
			   && $this->usercards
			   && $this->usercards->getSize();
	}

	private function initialize()
	{
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->usercards = [];
		}
		$customerId =  Mage::getSingleton('customer/session')->getCustomer()->getId();

		$this->usercards = Mage::getModel('ebanx/usercard')->getCustomerSavedCards($customerId);
	}
}
