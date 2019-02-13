<?php

class Plugincheck extends Mage_Core_Model_Abstract
{
    /**
    * Retrieves the plugincheck list
    * @return array
    */
    public static function getPlugincheckList()
    {
        $list = array();
        $list['php'] = phpversion();
        $list['sql'] = self::getDBVersion();
        $list['plugins'] = self::getModules();
        $list['configs'] = self::getEbanxConfigs();
        return $list;
    }

    /**
    * Retrieves the installed community modules
    * @return array
    */
    public static function getModules()
    {
        $module_list = array();
        $modules = (array) Mage::getConfig()->getNode('modules')->children();
        foreach (array_keys($modules) as $module_name) {
            $module_list[$module_name] = array(
                'version' => (string)$modules[$module_name]->version,
                'active'  => (string)$modules[$module_name]->active
            );
        }
        return $module_list;
    }

    /**
    * Retrieves the DB version
    * @return mixed
    */
    public static function getDBVersion()
    {
        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('externaldb_read');
        return $conn->fetchCol('SELECT version() AS version')[0];
    }

    /**
    * Retrieves the EBANX module configs
    * @return array
    */
    private static function getEbanxConfigs()
    {
        $configs = Mage::getSingleton('ebanx/api')->getConfig();

        return array(
            'max_installment'       => Mage::getStoreConfig('payment/ebanx_settings/auto_capture'),
            'sandbox_mode'          => $configs->isSandbox,
            'save_card_data'        => Mage::getStoreConfig('payment/ebanx_settings/save_card_data'),
            'one_click'             => Mage::getStoreConfig('payment/ebanx_settings/auto_capture'),
            'capture_enabled'       => Mage::getStoreConfig('payment/ebanx_settings/one_click_payment'),
            'show_local_amount'     => Mage::getStoreConfig('payment/ebanx_settings/iof_local_amount'),
            'enabled_payment_types' => self::getPaymentMethods(),
        );
    }

    /**
     * Retrieves the enabled payment methods
     * @return array
     */
    private static function getPaymentMethods()
    {
        $all_methods = array();
        $countries = array(
            array('brazil',    'br-'),
            array('chile',     'cl-'),
            array('colombia',  'co-'),
            array('peru',      'pe-'),
            array('mexico',    'mx-'),
            array('argentina', 'ar-'),
            array('ecuador',   'ec-'),
        );
        foreach ($countries as $country) {
            $methods = explode(",", Mage::getStoreConfig('payment/ebanx_settings/payment_methods_' . $country[0]));
            foreach ($methods as $method) {
                array_push($all_methods, $country[1] . $method);
            }
        }
        return $all_methods;
    }
}
