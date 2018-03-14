<?php
/**
 * Used to store some logs for payments using EBANX
 */

$installer = $this;
$installer->startSetup();

if (!$installer->tableExists('ebanx_logs')) {
	$installer->run("
		CREATE TABLE {$this->getTable('ebanx_logs')} (
			id int NOT NULL AUTO_INCREMENT,
			time datetime NOT NULL,
			event varchar(150) NOT NULL,
			log blob NOT NULL,
			UNIQUE KEY id (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	");
}

if (!$installer->tableExists('ebanx_store_lead')) {
	$installer->run("
		CREATE TABLE {$this->getTable('ebanx_store_lead')} (
			id int NOT NULL AUTO_INCREMENT,
			id_store int NOT NULL,
			id_lead varchar(150) NOT NULL,
			UNIQUE KEY id (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	");
}

$installer->endSetup();
