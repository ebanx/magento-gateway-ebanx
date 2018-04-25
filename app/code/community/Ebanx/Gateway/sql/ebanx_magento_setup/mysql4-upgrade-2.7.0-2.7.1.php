<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$tableName = $installer->getTable('sales_flat_order_payment');
$columnName = 'ebanx_hash';

if ($connection->tableColumnExists($tableName, $columnName) === true) {
    $installer->run("
    UPDATE $tableName SET
      ebanx_payment_hash = ebanx_hash
    WHERE
      ebanx_hash IS NOT NULL AND ebanx_payment_hash IS NULL
");
}

$installer->endSetup();
