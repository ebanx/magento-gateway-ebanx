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
			time datetime(6) NOT NULL DEFAULT NOW(6),
			event varchar(150) NOT NULL,
			integration_key VARCHAR(255) NOT NULL,
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

$entityTypeId = $installer->getEntityTypeId('customer_address');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

//this is for creating a new attribute for customer address entity
$installer->addAttribute("customer_address", "ebanx_document_type", array(
    "type" => "varchar",
    "backend" => "",
    "label" => "Delivery Instruction",
    "input" => "text",
    "source" => "",
    "visible" => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique" => false,
    "note" => "Custom Attribute Will Be Used Save Customer Document Type"
));

$attribute = Mage::getSingleton("eav/config")->getAttribute("customer_address", "ebanx_document_type");

$installer->addAttributeToGroup(
    $entityTypeId, $attributeSetId, $attributeGroupId, 'ebanx_document_type', '999'  //sort_order
);

$used_in_forms = array();

$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="checkout_register";
$used_in_forms[]="customer_account_create";
$used_in_forms[] = "customer_address_edit"; //this form code is used in checkout billing/shipping address
$used_in_forms[]="adminhtml_checkout";
$attribute->setData("used_in_forms", $used_in_forms)
          ->setData("is_used_for_customer_segment", true)
          ->setData("is_system", 0)
          ->setData("is_user_defined", 1)
          ->setData("is_visible", 1)
          ->setData("sort_order", 100)
;
$attribute->save();

$sales_quote_address = $installer->getTable('sales/quote_address');
$installer->getConnection()
          ->addColumn($sales_quote_address, 'ebanx_document_type', array(
              'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'New Delivery Instruction Field Added'
          ));

/**
 * Adding Extra Column to sales_flat_order_address
 * to store the delivery instruction field
 */
$sales_order_address = $installer->getTable('sales/order_address');
$installer->getConnection()
          ->addColumn($sales_order_address, 'ebanx_document_type', array(
              'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
              'comment' => 'New Delivery Instruction Field Added'
          ));

$installer->endSetup();
