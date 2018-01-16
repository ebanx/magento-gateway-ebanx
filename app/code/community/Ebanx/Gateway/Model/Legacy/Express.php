<?php
class Ebanx_Gateway_Model_Legacy_Express extends Mage_Payment_Model_Method_Abstract
{
	protected $_code = 'ebanx_express';
	protected $_isGateway          = true;
	protected $_isInitializeNeeded = false;

	protected $_infoBlockType = 'ebanx/info_legacy';
}
