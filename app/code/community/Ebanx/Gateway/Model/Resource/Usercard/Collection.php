<?php

class Ebanx_Gateway_Model_Resource_Usercard_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init('ebanx/usercard');
	}
}
