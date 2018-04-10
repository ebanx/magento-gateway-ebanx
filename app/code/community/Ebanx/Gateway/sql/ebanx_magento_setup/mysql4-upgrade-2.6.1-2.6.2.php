<?php
/**
 * Used to store some logs for payments using EBANX
 */

$installer = $this;
$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('customer_address');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('customer', 'ebanx_customer_document_type', array(
    'type' => Varien_Db_Ddl_Table::TYPE_VARCHAR
, 'is_user_defined' => 0
, 'label' => 'Customer Document Type'
, 'visible' => 1
, 'required' => 0
, 'user_defined' => 0
, 'nullable' => true
, 'searchable' => 0
, 'filterable' => 0
, 'comparable' => 0
, 'default' => null
));

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

$installer->endSetup();
