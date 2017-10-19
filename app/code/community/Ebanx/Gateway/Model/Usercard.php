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
	 * Remove the cards from array cards
	 *
	 * @param array $cards
	 * @param int   $userId
	 * @return Varien_Object
	 */
	public function removeCardsFromUser($cards, $userId)
	{
		$cards = $this->getCollectionByCustomerIdAndCardId($userId, $cards);

		foreach ($cards as $card) {
			$card->delete();
		}
	}

	/**
	 * Returns the registry by user id and masked number
	 *
	 * @param int    $userId
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
	 * @param int    $userId
	 * @return bool
	 */
	public function isCardAlreadySavedForCustomer($maskedNumber, $userId)
	{
		return $this->getCollectionByCustomerIdAndMaskedNumber($userId, $maskedNumber)->count() > 0;
	}

	/**
	 * Returns if the Card belongs to the customer
	 *
	 * @param string $token
	 * @param int    $userId
	 * @return bool
	 */
	public function doesCardBelongsToCustomer($token, $userId)
	{
		return $this->getCollectionByCustomerIdAndToken($userId, $token)->count() > 0;
	}

	public function getCustomerSavedCards($userId)
	{
		return $this->getCollection()
					->addFieldToFilter('user_id', $userId);
	}

	public function getPaymentMethodByToken($token)
	{
		$collection = $this->getCollection()
						   ->addFieldToFilter('token', $token);

		return $collection->getFirstItem()->getPaymentMethod();
	}

	/**
	 * Returns a collection by customer ID and Masked Number
	 *
	 * @param int    $userId
	 * @param string $maskedNumber
	 * @return Ebanx_Gateway_Model_Resource_Usercard_Collection
	 */
	private function getCollectionByCustomerIdAndMaskedNumber($userId, $maskedNumber)
	{
		return $this->getCollection()
					->addFieldToFilter('user_id', $userId)
					->addFieldToFilter('masked_number', $maskedNumber);
	}

	/**
	 * Returns a collection by customer ID and Card ID
	 *
	 * @param int   $userId
	 * @param array $cardId
	 * @return Ebanx_Gateway_Model_Resource_Usercard_Collection
	 */
	private function getCollectionByCustomerIdAndCardId($userId, $cardId)
	{
		return $this->getCollection()
					->addFieldToFilter('user_id', $userId)
					->addFieldToFilter('ebanx_card_id', array('in' => $cardId));
	}

	/**
	 * Returns a collection by customer ID and Token
	 *
	 * @param int    $userId
	 * @param string $token
	 * @return Ebanx_Gateway_Model_Resource_Usercard_Collection
	 */
	private function getCollectionByCustomerIdAndToken($userId, $token)
	{
		return $this->getCollection()
					->addFieldToFilter('user_id', $userId)
					->addFieldToFilter('token', $token);
	}
}
