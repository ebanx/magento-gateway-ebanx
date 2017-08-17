<?php

class Ebanx_Gateway_OneclickController extends Mage_Core_Controller_Front_Action
{
	public function payAction()
	{
		$test = Mage::app()->getRequest()->getPost();
		echo '<pre>';
		var_dump($test);
		echo '</pre>';
	}
}
