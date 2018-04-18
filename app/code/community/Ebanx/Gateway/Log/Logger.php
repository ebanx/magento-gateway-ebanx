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
     */
    final protected static function save($event, array $log_data)
    {
        $logModel = new Ebanx_Gateway_Model_Log();

        $logModel->setEvent($event);
        $logModel->setLog(json_encode($log_data));

        $logModel->save();
    }

    /**
     * @param string $event event
     * @return Ebanx_Gateway_Model_Resource_Log_Collection
     */
    final public static function lastByEvent($event = 'plugin_status_change')
    {
        $logModel = new Ebanx_Gateway_Model_Log();

        $col = $logModel->getCollection()
            ->addFieldToSelect(array('log'))
            ->addFieldToFilter('event', $event);

        $col->getSelect()
            ->order('id DESC')
            ->limit(1);

        return $col;
    }

    /**
     * @return void
     */
    final public static function truncate()
    {
        Mage::getResourceModel('ebanx/log')->truncate();
    }

    /**
     * @return array
     */
    final public static function fetch()
    {
        $logModel = new Ebanx_Gateway_Model_Log();

        $col = $logModel->getCollection();

        $col->getSelect()
            ->order('id DESC');

        $res = array();

        foreach ($col as $log) {
            $res[] = $log->getData();
        }

        return $res;
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
