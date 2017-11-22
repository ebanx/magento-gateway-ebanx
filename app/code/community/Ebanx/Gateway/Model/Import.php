<?php

class Ebanx_Gateway_Model_Import extends Mage_Directory_Model_Currency_Import_Abstract
{
	protected $_messages = array();
	/**
	 * @var \Ebanx\Benjamin\Facade
	 */
	protected $ebanx;

	/**
	 * Retrieve rate
	 *
	 * @param   string $currencyFrom
	 * @param   string $currencyTo
	 *
	 * @return  float
	 */
	protected function _convert( $currencyFrom, $currencyTo ) {
		$this->ebanx = Mage::getSingleton('ebanx/api')->ebanx();
		$rate = $this->ebanx->exchange()->fetchRate($currencyFrom, $currencyTo);
		if ($rate === 0) {
			return null;
		}
		return $rate;
	}
}
