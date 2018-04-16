<?php

require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

abstract class Ebanx_Gateway_Log_Logger
{
	/**
	 * Method responsible to save log on database using log model
	 *
	 * @param string $event event name to be logged.
	 * @param array  $log_data data to be logged.
	 */
	final protected static function save($event, array $log_data) {
		$logModel = new Ebanx_Gateway_Model_Log();
        $integrationKey = Mage::helper('ebanx/data')->getIntegrationKey();

		$logModel->setEvent($event);
		$logModel->setLog(json_encode($log_data));
		$logModel->setIntegrationKey($integrationKey);

		$logModel->save();
	}

	final public static function lastByEvent($event = 'plugin_status_change') {
		$logModel = new Ebanx_Gateway_Model_Log();

		$col = $logModel->getCollection()
            ->addFieldToSelect(array('log'))
            ->addFieldToFilter('event', $event);

		$col->getSelect()
            ->order('id DESC')
            ->limit(1);

        return $col;
	}

	final public static function delete($col) {
       foreach($col as $log)
            $log->delete();
	}

	final public static function fetch($integrationKey) {
		$logModel = new Ebanx_Gateway_Model_Log();

		$col = $logModel->getCollection();

		$col->addFieldToFilter('integration_key', $integrationKey)
            ->getSelect()
            ->order('id DESC');

        $res = array();

        foreach ($col as $log) {
        	$res[] = $log->getData();
        }

        return array($col ,$res);
	}

	/**
	 * Abstract method that must be overrated by child classes
	 *
	 * This method is responsible for receive log data, manage them and send them to method save
	 *
	 * @param array $log_data data to be logged.
	 */
	abstract public static function persist(array $log_data = array());
}
