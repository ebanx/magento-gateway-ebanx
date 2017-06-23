<?php

class Ebanx_Gateway_Model_Source_Duedate
{
	const MIN_DUE_DATE_DAYS = 1;
	const MAX_DUE_DATE_DAYS = 3;

	public function toOptionArray()
	{
		$options = array();
		for ($i = self::MIN_DUE_DATE_DAYS; $i <= self::MAX_DUE_DATE_DAYS; $i++) {
			$options[] = array('value' => $i, 'label' => $i);
		}
		return $options;
	}
}
