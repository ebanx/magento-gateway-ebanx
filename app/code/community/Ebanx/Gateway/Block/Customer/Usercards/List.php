<?php
class Ebanx_Gateway_Block_Customer_Usercards_List extends Mage_Core_Block_Template
{
	private $cards = null;

	private function getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}

	/**
	 * Wrapper for getItems() method
	 *
	 * @return array
	 */
	public function getCards()
	{
		if (is_null($this->cards)) {
			$this->cards = $this->getItems();
		}

		return $this->cards;
	}

	/**
	 * Constructor. Prepare user cards list
	 *
	 * @return Mage_Core_Block_Template
	 */
	public function __construct()
	{
		parent::__construct();

		$items = Mage::getResourceModel('ebanx/usercard_collection')
			->addFieldToFilter('user_id', $this->getCustomer()->getId())
			->addOrder('ebanx_card_id', 'desc');

		$this->setItems($items);

		return $this;
	}

	/**
	 * Prepare layout
	 *
	 * @return Mage_Core_Block_Template
	 */
	protected function _prepareLayout()
	{
		parent::_prepareLayout();

		$pager = $this->getLayout()->createBlock('page/html_pager', 'ebanx.customer.cards.pager')
			->setCollection($this->getItems());

		$this->setChild('pager', $pager);

		$this->getItems()->load();

		return $this;
	}

	/**
	 * Get form action URL
	 *
	 * @return string
	 */
	public function getFormUrl()
	{
		return Mage::getUrl('ebanx/customer/removecard');
	}

	/**
	 * Get "Back" URL
	 *
	 * @return string
	 */
	public function getBackUrl()
	{
		if ($this->getRefererUrl()) {
			$url = $this->getRefererUrl();
		} else {
			$url = Mage::getUrl('customer/account/');
		}

		return $url;
	}

	/**
	 * Get Add new card URL
	 *
	 * @return string
	 */
	public function getAddCardUrl()
	{
		return Mage::getUrl('ebanx/customer/cardadd');
	}
}
