<?php

class Ebanx_Gateway_Block_Adminhtml_System_Config_Form_Field_Interest
	extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	public function __construct()
	{
		$this->addColumn('instalments', array(
			'label' => Mage::helper('ebanx')->__('Up To'),
			'style' => 'width:120px',
		));

		$this->addColumn('interest', array(
			'label' => Mage::helper('ebanx')->__('Interest Rate'),
			'style' => 'width:120px',
		));

		$this->_addAfter = false;

		parent::__construct();
	}

	/**
	 * Render array cell for prototypeJS template
	 *
	 * @param string $columnName
	 * @return string
	 */
	protected function _renderCellTemplate($columnName)
	{
		if (empty($this->_columns[$columnName])) {
			throw new Exception('Wrong column name specified.');
		}

		$column = $this->_columns[$columnName];
		$elementName = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
		$extraParams = ' data-value="#{' . $columnName . '}" ' .
			(isset($column['style']) ? ' style="' . $column['style'] . '"' : '');

		if ($columnName == 'instalments') {
			return $this->getInstallmentsSelectHtml($elementName, $extraParams);
		}
		return parent::_renderCellTemplate($columnName);
	}

	/**
	 * @param $name
	 * @param $extraParams
	 * @return string
	 */
	public function getInstallmentsSelectHtml($name, $extraParams)
	{
		$select = $this->getLayout()->createBlock('adminhtml/html_select')
			->setName($name)
			->setClass('select-instalments')
			->setExtraParams($extraParams)
			->setOptions(Mage::getSingleton('ebanx/source_instalment')->toOptionArray());

		return $select->getHtml();
	}

	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		$fieldId = $this->getElement()->getId();

		$html = "<div id=\"$fieldId\">";
		$html .= parent::_toHtml();
		$html .= Mage::helper('adminhtml/js')->getScript(
			"$$('.select-instalments').each(function(el){ el.value = el.readAttribute('data-value'); });\n"
		);
		$html .= '</div>';

		return $html;
	}
}
