<?php

require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

class Ebanx_Gateway_Log_Environment
{
    /**
     * @return array
     */
    public static function getPlatformInfo()
    {
        $environment = self::getEnvironment();

        return array(
            'platform' => array(
                'name' => 'Magento',
                'version' => $environment->platform->version,
                'theme' => self::getThemeData(),
                'plugins' => self::getPluginsData(),
                'store_id' => Mage::app()->getStore()->getWebsiteId(),
            ),
            'server' => array(
                'language' => $environment->interpreter,
                'web_server' => $environment->web_server,
                'database_server' => $environment->database_server,
                'os' => $environment->operating_system,
            ),
        );
    }

    /**
     * @return stdClass
     */
    private static function getEnvironment()
    {
        $environment = new stdClass();
        $environment->platform = new stdClass();
        $environment->platform->name = 'Magento';
        $environment->platform->version = Mage::getVersion();

        $environment->interpreter = new stdClass();
        $environment->interpreter->name = 'PHP';
        $environment->interpreter->version = PHP_VERSION;

        $environment->web_server = new stdClass();
        $environment->web_server->signature = isset($_SERVER['SERVER_SIGNATURE']) ? $_SERVER['SERVER_SIGNATURE'] : '';

        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('externaldb_read');

        $environment->database_server = new stdClass();
        $environment->database_server->name = 'MySQL';
        $environment->database_server->version = $conn->fetchCol('SELECT version() AS version')[0];

        $environment->operating_system = new stdClass();
        $environment->operating_system->name = PHP_OS;
        $environment->operating_system->version = self::extractVersionNumberFrom(php_uname('v'));

        return $environment;
    }

    /**
     * @return array
     */
    private static function getPluginsData()
    {
        return (array) array_map(function ($plugin) {
            $plugin->status = Mage::helper('core')->isModuleOutputEnabled($plugin->getName()) ? 'enabled' : 'disabled';

            return $plugin;
        }, (array) Mage::getConfig()->getNode('modules')->children());
    }

    /**
     * @return array
     */
    private static function getThemeData()
    {
        return array_map(function ($v) {
            return (object) array( $v => Mage::getSingleton('core/design_package')->getTheme($v));
        }, array(
            'locale',
            'layout',
            'template',
            'default',
            'frontend',
            'skin'
        ));
    }

    /**
     * @param string $haystack haystack to extract version number
     *
     * @return string
     */
    private static function extractVersionNumberFrom($haystack)
    {
        preg_match('/((\d)+(\.|\D))+/', $haystack, $version_candidates_array);
        if (count($version_candidates_array) > 0 && strlen($version_candidates_array[0]) > 0) {
            $version_candidates_array[0] = str_replace('.', '_', $version_candidates_array[0]);
            $version_candidates_array[0] = preg_replace('/[\W]/', '', $version_candidates_array[0]);
            $version_candidates_array[0] = str_replace('_', '.', $version_candidates_array[0]);
            $version                     = $version_candidates_array[0];
        } else {
            $version = 'Unknown';
        }
        return $version;
    }
}
