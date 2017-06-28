<?php

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

	public function formatInstalment($instalment)
	{
		$amount = Mage::helper('core')->formatPrice($instalment->baseAmount, false);
		$instalmentNumber = $instalment->instalmentNumber;
		$interestMessage = $this->getInterestMessage($instalment->hasInterests);
		$message = sprintf('%sx de %s %s', $instalmentNumber, $amount, $interestMessage);
		return $message;
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
