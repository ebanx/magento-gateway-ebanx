<?php

class Ebanx_Gateway_Model_Source_Instalment
{
	const MIN_INSTALMENTS = 1;
	const MAX_INSTALMENTS = 12;

	public function toOptionArray()
	{
		$options = array();
		for ($i = self::MIN_INSTALMENTS; $i <= self::MAX_INSTALMENTS; $i++) {
			$options[] = array('value' => $i, 'label' => $i . 'x');
		}
		return $options;
	}
}
