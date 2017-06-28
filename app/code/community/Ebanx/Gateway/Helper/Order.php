<?php

class Ebanx_Gateway_Helper_Order extends Ebanx_Gateway_Helper_Data
{
	public function getOrderByHash($hash)
	{
		$model = Mage::getModel('sales/order_payment')
			->getCollection()
			->setPageSize(1)
			->setCurPage(1)
			->addFieldToFilter('ebanx_payment_hash', $hash)
			->load();

		if ($model->count() !== 1) {
			Mage::throwException($this->__('EBANX: Invalid payment hash. We couldn\'t find the order.'));
		};

		$payment = $model->getFirstItem();

		return Mage::getModel('sales/order')->load($payment->getParentId());
	}

	public function getEbanxMagentoOrder($ebanxStatus)
	{
		$status = [
			'CO' => Mage::getStoreConfig('payment/ebanx_settings/payment_co_status'),
			'PE' => Mage::getStoreConfig('payment/ebanx_settings/payment_pe_status'),
			'OP' => Mage::getStoreConfig('payment/ebanx_settings/payment_op_status'),
			'CA' => Mage::getStoreConfig('payment/ebanx_settings/payment_ca_status')
		];

		return $status[strtoupper($ebanxStatus)];
	}

	public function getTranslatedOrderStatus($ebanxStatus)
	{
		$status = [
			'CO' => 'Confirmed',
			'PE' => 'Pending',
			'OP' => 'Open',
			'CA' => 'Canceled'
		];

		return $status[strtoupper($ebanxStatus)];
	}
}
