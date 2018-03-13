<?php 

class Ebanx_Gateway_Model_Observer extends Varien_Event_Observer
{
	public function observeConfigSection($observer)
	{
		$store = Mage::app()->getStore();
		$user = Mage::getSingleton('admin/session');

		$url = 'https://dashboard.ebanx.com/api/lead';
		$args = array(
			'body' => array(
				'lead' => array(
					'user_email' => $user->getEmail(),
					'user_last_name' => $user->getLastname(),
					'user_first_name' => $user->getFirstname(),
					'site_email' => Mage::getStoreConfig('trans_email/ident_sales/email'),
					'site_url' => $store->getHomeUrl(),
					'site_name' => $store->getFrontendName(),
					'site_language' => Mage::app()->getLocale()->getLocaleCode(),
					'magento_version' => Mage::getVersion(),
				),
			),
		);

		$ch = curl_init($url);

		curl_setopt_array($ch, array(
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $args,
			CURLOPT_RETURNTRANSFER => true,
		));

		$transfer = curl_exec($ch);
	}
}
