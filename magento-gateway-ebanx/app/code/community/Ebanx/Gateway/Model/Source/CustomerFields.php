<?php

class Ebanx_Gateway_Model_Source_CustomerFields
{
	private function getAttributesByEntityCode($entity)
	{
		$type = Mage::getSingleton('eav/config')->getEntityType($entity);
		$attributes = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($type->getId())->getItems();
		$data = [];

		foreach ($attributes as $attribute) {
			if ($attribute->getIsVisible() && $attribute->getAttributeCode() !== '') {
				$data[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
			}
		}

		return $data;
	}

	public function toOptionArray()
	{
		return $this->getAttributesByEntityCode('customer');
	}
}