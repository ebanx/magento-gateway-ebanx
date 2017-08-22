<?php

class Ebanx_Gateway_OneclickController extends Mage_Core_Controller_Front_Action
{
	/**
	 * @var Mage_Catalog_Model_Product
	 */
	private $product;

	/**
	 * @var array
	 */
	private $request;

	protected function _construct()
	{
		parent::_construct();

		$this->request = Mage::app()->getRequest()->getPost();
		$this->product = Mage::getModel('catalog/product')->load($this->request['product']);
	}

	public function payAction()
	{
		$product['id'] = $this->getRealProductId();

		echo '<pre>';
		var_dump($product['id']);
		echo '</pre>';
	}

	/**
	 * If the product type isn't simple it returns the real product id
	 *
	 * @return string
	 */
	private function getRealProductId()
	{
		switch ($this->product->getTypeId()) {
			case 'simple':
				return $this->request['product'];
			case 'bundle':
			case 'configurable':
			case 'downloadable':
			case 'grouped':
			case 'virtual':
			default:
				return '';
		}
	}
}
