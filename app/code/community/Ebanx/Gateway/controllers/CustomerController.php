<?php
class Ebanx_Gateway_CustomerController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Check customer authentication
	 *
	 * @return Mage_Core_Controller_Front_Action
	 */
	public function preDispatch()
	{
		parent::preDispatch();

		$loginUrl = Mage::helper('customer')->getLoginUrl();

		if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
		}

		return $this;
	}

	/**
	 * Display list of customer's cards
	 *
	 * @return void
	 */
	public function usercardsAction()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');

		$block = $this->getLayout()->getBlock('ebanx_customer_usercards_list');
		$block->setRefererUrl($this->_getRefererUrl());

		$headBlock = $this->getLayout()->getBlock('head');
		$headBlock->setTitle(Mage::helper('ebanx')->__('My Payment Cards'));

		$this->renderLayout();
	}

	/**
	 * Remove card action
	 *
	 * @return void
	 */
	public function removecardAction()
	{
		$cardsToRemove = $this->getRequest()->getParam('card');

		if (!empty($cardsToRemove)) {
			Mage::getModel('ebanx/usercard')->removeCardsFromUser(
				$cardsToRemove,
				Mage::getSingleton('customer/session')->getCustomerId()
			);
			Mage::getSingleton('customer/session')->addSuccess('The cards have been removed successfully.');
		}

		$this->_redirect('ebanx/customer/usercards');
	}
}
