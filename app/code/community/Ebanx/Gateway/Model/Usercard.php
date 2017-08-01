<?php

class Ebanx_Gateway_Model_Usercard extends Mage_Core_Model_Abstract
{
	/**
	 * Internal constructor
	 */
	protected function _construct()
	{
		$this->_init('ebanx/usercard');
	}

	/**
	 * Returns the registry by user id and masked number
	 *
	 * @param int $userId
	 * @param string $maskedNumber
	 * @return Varien_Object
	 */
	public function getByCustomerIdAndMaskedNumber($userId, $maskedNumber)
	{
		return $this->getCollectionByCustomerIdAndMaskedNumber($userId, $maskedNumber)->getFirstItem();
	}

	/**
	 * Returns if a Card is already saved for the customer
	 *
	 * @param string $maskedNumber
	 * @param int $userId
	 * @return bool
	 */
	public function isCardAlreadySavedForCustomer($maskedNumber, $userId)
	{
		return $this->getCollectionByCustomerIdAndMaskedNumber($userId, $maskedNumber)->count() > 0;
	}

	/**
	 * Returns a collection by customer ID and Masked Number
	 *
	 * @param int $userId
	 * @param string $maskedNumber
	 * @return Ebanx_Gateway_Model_Resource_Usercard_Collection
	 */
	private function getCollectionByCustomerIdAndMaskedNumber($userId, $maskedNumber)
	{
		return $this->getCollection()
			->addFieldToFilter('user_id', $userId)
			->addFieldToFilter('masked_number', $maskedNumber);
	}
}
