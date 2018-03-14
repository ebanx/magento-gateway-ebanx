<?php

class Ebanx_Gateway_Model_Resource_Lead extends Mage_Core_Model_Resource_Db_Abstract
{
	protected function _construct()
	{
		$this->_init('ebanx/lead', 'id');
	}
}
