<?php

abstract class Ebanx_Gateway_Block_Info_Abstract extends Mage_Payment_Block_Info
{
	private function getTotal()
	{
		return $this->getMethod()->getTotal();
	}

	private function formatPriceWithLocalCurrency($currency, $price)
	{
		return Mage::app()->getLocale()->currency($currency)->toCurrency($price);
	}

	public function getLocalAmount($currency, $formatted = true)
	{
		$amount = round(Mage::helper('ebanx')->getLocalAmountWithTax($currency, $this->getTotal()), 2);

		if ($this->shouldntShowIof()) {
			$amount = round(Mage::helper('ebanx')->getLocalAmountWithoutTax($currency, $this->getTotal()), 2);
		}

		return $formatted ? $this->formatPriceWithLocalCurrency($currency, $amount) : $amount;
	}

	public function getLocalAmountWithoutTax($currency, $formatted = true){
		return $formatted ? $this->formatPriceWithLocalCurrency($currency, $this->getTotal()) : $this->getTotal();
	}

	public function shouldntShowIof() {
		return Mage::getStoreConfig('payment/ebanx_settings/iof_local_amount') === '0';
	}

	protected function isAdmin() {
		if(Mage::app()->getStore()->isAdmin())
		{
			return true;
		}

		if(Mage::getDesign()->getArea() == 'adminhtml')
		{
			return true;
		}

		return false;
	}

	protected function getDashboardUrl($hash) {
		return sprintf(
			'https://dashboard.ebanx.com%s/payments/?hash=%s',
			$this->getInfo()->getEbanxEnvironment() === 'sandbox'
				? '/test'
				: '',
			$hash
		);
	}

	protected function getNotificationUrl($hash) {
		return $this->getUrl(
			'ebanx/payment/notify',
			array(
				'hash_codes' => $hash,
				'operation' => 'update',
				'notification_type' => 'forced',
			)
		);
	}
}
