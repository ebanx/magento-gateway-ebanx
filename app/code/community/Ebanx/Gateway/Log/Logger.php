<?php

require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

abstract class Ebanx_Gateway_Log_Logger
{
    /**
     * Method responsible to save log on database using log model
     *
     * @param string $event    event name to be logged.
     * @param array  $log_data data to be logged.
     *
     * @return void
     * @throws Exception If saving model fails.
     */
    final protected static function save($event, array $log_data)
    {
        $logModel = new Ebanx_Gateway_Model_Log();
        $integrationKey = Mage::helper('ebanx/data')->getIntegrationKey();

        $logModel->setEvent($event);
        $logModel->setLog(json_encode($log_data));
        $logModel->setIntegrationKey($integrationKey);

        $logModel->save();
    }

    /**
     * @param string $event event
     *
     * @return Ebanx_Gateway_Model_Resource_Log_Collection
     */
    final public static function lastByEvent($event = 'plugin_status_change')
    {
        $logModel = new Ebanx_Gateway_Model_Log();

        $row = $logModel->getCollection()
            ->addFieldToSelect(array('log'))
            ->addFieldToFilter('event', $event);

        $row->getSelect()
            ->order('id DESC')
            ->limit(1);

        return $row;
    }

    /**
     * @param Ebanx_Gateway_Model_Resource_Log_Collection $row Row to be deleted
     *
     * @return void
     */
    final public static function delete($row)
    {
        foreach ($row as $log) {
            $log->delete();
        }
    }

    /**
     * @param string $integrationKey Merchant integration key
     *
     * @return array
     */
    final public static function fetch($integrationKey)
    {
        $logModel = new Ebanx_Gateway_Model_Log();

        $row = $logModel->getCollection();

        $row->addFieldToFilter('integration_key', $integrationKey)
            ->getSelect()
            ->order('id DESC');

        $res = array();

        foreach ($row as $log) {
            $res[] = $log->getData();
        }

        return array($row ,$res);
    }

    /**
     * Abstract method that must be overrated by child classes
     *
     * This method is responsible for receive log data, manage them and send them to method save
     *
     * @param array $log_data data to be logged.
     *
     * @return void
     */
    abstract public static function persist(array $log_data = array());
}
