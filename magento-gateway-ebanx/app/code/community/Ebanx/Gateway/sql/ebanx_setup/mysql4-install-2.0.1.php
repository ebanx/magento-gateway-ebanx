<?php
$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('order_payment', 'ebanx_payment_hash', array(
    'type'            => Varien_Db_Ddl_Table::TYPE_VARCHAR
  , 'backend_type'    => 'text'
  , 'frontend_input'  => 'text'
  , 'is_user_defined' => 0
  , 'label'           => 'EBANX Hash'
  , 'visible'         => 1
  , 'required'        => 0
  , 'user_defined'    => 0
  , 'searchable'      => 1
  , 'filterable'      => 0
  , 'comparable'      => 0
  , 'default'         => null
));

$installer->addAttribute('order_payment', 'ebanx_due_date', array(
    'type'            => Varien_Db_Ddl_Table::TYPE_DATETIME
  , 'is_user_defined' => 0
  , 'label'           => 'Due Date'
  , 'visible'         => 1
  , 'required'        => 0
  , 'user_defined'    => 0
  , 'nullable'        => true
  , 'filterable'      => 0
  , 'comparable'      => 0
  , 'default'         => null
));

$installer->addAttribute('order_payment', 'ebanx_bar_code', array(
    'type'            => Varien_Db_Ddl_Table::TYPE_VARCHAR
  , 'is_user_defined' => 0
  , 'label'           => 'Bar Code'
  , 'visible'         => 1
  , 'required'        => 0
  , 'user_defined'    => 0
  , 'nullable'        => true
  , 'searchable'      => 0
  , 'filterable'      => 0
  , 'comparable'      => 0
  , 'default'         => null
));


$installer->endSetup();