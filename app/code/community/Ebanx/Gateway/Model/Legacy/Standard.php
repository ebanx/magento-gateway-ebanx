<?php
class Ebanx_Gateway_Model_Standard extends Ebanx_Gateway_Payment
{
    protected $_code = 'ebanx_standard';
    protected $_isGateway          = true;
    protected $_isInitializeNeeded = false;

    protected $_infoBlockType = 'ebanx/info_legacy';
}