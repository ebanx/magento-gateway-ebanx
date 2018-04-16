<?php

require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

final class Ebanx_Gateway_Log_Logger_SettingsChange extends Ebanx_Gateway_Log_Logger
{
	public static function persist(array $log_data = array()) {
		parent::save(
			'settings_change',
			array_merge(
				Ebanx_Gateway_Log_Environment::get_platform_info(),
				$log_data
			)
		);
	}
}
