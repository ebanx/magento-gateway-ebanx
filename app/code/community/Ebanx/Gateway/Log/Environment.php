<?php

require_once Mage::getBaseDir('lib') . '/Ebanx/vendor/autoload.php';

class Ebanx_Gateway_Log_Environment
{
	public $platform;

	public $interpreter;

	public $web_server;

	public $database_server;

	public $operating_system;

	public function __construct() {
		$this->platform = new stdClass();
		$this->platform->name = 'Magento';
		$this->platform->version = Mage::getVersion();

		$this->interpreter = new stdClass();
		$this->interpreter->name = 'PHP';
		$this->interpreter->version = PHP_VERSION;

		$this->web_server = new stdClass();
		$this->web_server->signature = $_SERVER['SERVER_SIGNATURE'];

		$resource = Mage::getSingleton('core/resource');
		$conn = $resource->getConnection('externaldb_read');

		$this->database_server = new stdClass();
		$this->database_server->name = 'MySQL';
		$this->database_server->version = $conn->fetchCol('SELECT version() AS version')[0];

		$this->operating_system = new stdClass();
		$this->operating_system->name = PHP_OS;
		$this->operating_system->version = $this->extract_version_number_from(php_uname('v'));
	}

	protected function extract_version_number_from($haystack) {
		preg_match( '/((\d)+(\.|\D))+/', $haystack, $version_candidates_array );
		if ( count( $version_candidates_array ) > 0 && strlen( $version_candidates_array[0] ) > 0 ) {
			$version_candidates_array[0] = str_replace( '.', '_', $version_candidates_array[0] );
			$version_candidates_array[0] = preg_replace( '/[\W]/', '', $version_candidates_array[0] );
			$version_candidates_array[0] = str_replace( '_', '.', $version_candidates_array[0] );
			$version                     = $version_candidates_array[0];
		} else {
			$version = 'Unknown';
		}
		return $version;
	}

	public function __toString() {
		return json_encode($this, JSON_PRETTY_PRINT);
	}
}
