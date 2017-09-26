<?php
/**
 * Used to store saved customer payment cards
 */

$installer = $this;
$installer->startSetup();

if (!$installer->tableExists('ebanx_user_cards')) {

	$installer->run("
	CREATE TABLE {$this->getTable('ebanx_user_cards')} (
	    `ebanx_card_id` int(11) unsigned NOT NULL auto_increment,
	    `user_id` int(11) NOT NULL default 0,
	    `token` varchar(255) NOT NULL default '',
	    `masked_number` varchar(20) NOT NULL default '',
	    `brand` varchar(255) NOT NULL default '',
	    `payment_method` varchar(255) NOT NULL default '',
	     PRIMARY KEY (ebanx_card_id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
}


$installer->endSetup();
