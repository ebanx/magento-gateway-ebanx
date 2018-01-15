<?php
class Ebanx_Gateway_Model_Express extends Ebanx_Gateway_Payment
{
    protected $_code = 'ebanx_express';
    protected $_isGateway          = true;
    protected $_isInitializeNeeded = false;

    protected $_infoBlockType = 'ebanx/info_legacy';
}