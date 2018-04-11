<?php

class Ebanx_Gateway_Model_Observer extends Varien_Event_Observer
{
    /**
     * @param observer $observer event observer
     *
     * @return void
     */
    public function observeAdvancedSection($observer)
    {
        $to = Mage::helper('core')->isModuleOutputEnabled('Ebanx_Gateway');
        $toData = $to ? array('to' => 'enabled') : array('to' => 'disabled');
        $prev_status = null;

        $col = Ebanx_Gateway_Log_Logger::lastByEvent();

        foreach ($col as $item) {
            // phpcs:disable
            if (!empty($item->getData())) {
                // phpcs:enable
                $prev_status = json_decode($item->getData()['log'])->to;
            }
        }

        if (is_null($prev_status) || (!$to && $prev_status === 'enabled') || ($to && $prev_status !== 'enabled')) {
            Ebanx_Gateway_Log_Logger_PluginStatusChange::persist($toData);
        }
    }

    /**
     * @param observer $observer event observer
     *
     * @return void
     */
    public function observeConfigSection($observer)
    {
        Ebanx_Gateway_Log_Logger_SettingsChange::persist(array(
            'settings' => Mage::getStoreConfig('payment/ebanx_settings')
        ));

        $store = Mage::app()->getStore();
        $leadModel = new Ebanx_Gateway_Model_Lead();

        $lead = $leadModel->load($store->getWebsiteId(), 'id_store')->getData();

        if (!empty($lead)) {
            $helperEbanxData = Mage::helper('ebanx/data');
            $leadData = array(
                'id' => $lead['id_lead'],
                'integration_key' => $helperEbanxData->getIntegrationKey(),
            );
        } else {
            $user = Mage::getSingleton('admin/session')->getUser();
            $leadData = array(
                'user_email' => $user->getEmail(),
                'user_last_name' => $user->getLastname(),
                'user_first_name' => $user->getFirstname(),
                'site_email' => Mage::getStoreConfig('trans_email/ident_sales/email'),
                'site_url' => Mage::getBaseUrl(),
                'site_name' => $store->getFrontendName(),
                'site_language' => Mage::app()->getLocale()->getLocaleCode(),
                'magento_version' => Mage::getVersion(),
                'type' => 'Magento',
            );
        }

        $data = json_encode(
            array('lead' => $leadData)
        );

        $this->doCurl($data, $store->getWebsiteId(), (isset($user)));
    }

    /**
     * @param string $data    encoded json data
     * @param string $storeId store id
     * @param bool   $new     is lead new
     *
     * @return void
     */
    private function doCurl($data, $storeId, $new = true)
    {
        // phpcs:disable
        $ch = curl_init('https://dashboard.ebanx.com/api/lead');

        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $transfer = curl_exec($ch);
        $error = curl_error($ch);
        // phpcs:enable

        if ($new && empty($error)) {
            $leadInfo = json_decode($transfer, true);

            if (isset($leadInfo['id'])) {
                $leadModel = new Ebanx_Gateway_Model_Lead();
                $leadModel->setIdLead($leadInfo['id']);
                $leadModel->setIdStore($storeId);

                $leadModel->save();
            }
        }
    }
}
