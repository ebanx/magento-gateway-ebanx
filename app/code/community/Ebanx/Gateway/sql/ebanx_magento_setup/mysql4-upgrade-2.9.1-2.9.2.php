<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$installer->addAttribute(
    'order',
    'ebanx_interest_amount',
    array(
        'type' => 'varchar',
        'grid' => true,
    )
);

$installer->addAttribute(
    'quote',
    'ebanx_amount_with_interest',
    array(
        'type' => 'varchar',
        'grid' => false,
    )
);

$installer->addAttribute(
    'quote_address',
    'ebanx_interest_amount',
    array(
        'type' => 'varchar',
        'grid' => false,
    )
);

$installer->endSetup();
