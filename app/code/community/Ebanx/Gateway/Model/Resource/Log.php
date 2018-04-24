<?php

class Ebanx_Gateway_Model_Resource_Log extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ebanx/log', 'id');
    }

    /**
     * @return $this
     */
    public function truncate()
    {
        $this->_getWriteAdapter()->query('TRUNCATE TABLE '.$this->getMainTable());

        return $this;
    }
}
